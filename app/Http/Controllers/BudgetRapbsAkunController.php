<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Unit;
use App\Models\Akuntan_Unit;
use Illuminate\Http\Request;
use App\Models\Budget_Rapbs_Akun;
use App\Models\Sub_Kategori_Akun;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BudgetRapbsAkunController extends Controller
{
    
    public function index(Request $request)
    {
        $user = Auth::user();

        if (in_array($user->role, ['admin', 'auditor'])) {
            $id_unit = $request->get('unit', 'all');
            $units = Unit::all();
            $nama_unit = null;

            if ($id_unit !== 'all') {
                $nama_unit = Unit::where('id_unit', $id_unit)->value('unit');
            }
        } elseif ($user->role === 'akuntan_unit') {
            $akuntanUnit = Akuntan_Unit::where('id_akuntan_unit', $user->id_user)->firstOrFail();
            $id_unit = $akuntanUnit->id_unit;
            $units = collect(); // kosongkan select
            $nama_unit = Unit::where('id_unit', $id_unit)->value('unit');
        } else {
            abort(403, 'Role tidak dikenal');
        }

        if ($id_unit === 'all') {
            $akun = Akun::select(
                'akun.id_akun',
                'akun.kode_akun',
                'akun.akun',
                DB::raw('SUM(akun.saldo_awal_debit) as saldo_awal_debit'),
                DB::raw('SUM(akun.saldo_awal_kredit) as saldo_awal_kredit'),
                DB::raw('SUM(budget_rapbs_akun.budget_rapbs_akun) as budget_rapbs')
            )
            ->leftJoin('budget_rapbs_akun', 'akun.id_akun', '=', 'budget_rapbs_akun.id_akun')
            ->groupBy('akun.id_akun', 'akun.kode_akun', 'akun.akun')
            ->orderBy('akun.kode_akun')
            ->get();
        } else {
            $akun = Akun::select(
                'akun.id_akun',
                'akun.kode_akun',
                'akun.akun',
                'akun.saldo_awal_debit',
                'akun.saldo_awal_kredit',
                DB::raw('COALESCE(budget_rapbs_akun.budget_rapbs_akun, 0) as budget_rapbs')
            )
            ->leftJoin('budget_rapbs_akun', function ($join) use ($id_unit) {
                $join->on('akun.id_akun', '=', 'budget_rapbs_akun.id_akun')
                    ->where('budget_rapbs_akun.id_unit', '=', $id_unit);
            })
            ->orderBy('akun.kode_akun')
            ->get();
        }

        return view('budget-rapbs-akun', compact('akun', 'units', 'id_unit', 'user', 'nama_unit'));
    }





    public function storeOrUpdate(Request $request)
    {
        DB::statement("SET @current_user_id = " . auth()->id());

        $validated = $request->validate([
            'id_akun' => 'required|integer',
            'id_unit' => 'required|integer',
            'budget_rapbs_akun' => 'required|numeric',
        ]);

        $existing = Budget_Rapbs_Akun::where('id_akun', $validated['id_akun'])
                                    ->where('id_unit', $validated['id_unit'])
                                    ->first();

        if ($existing) {
            $existing->budget_rapbs_akun = $validated['budget_rapbs_akun']; // fix di sini
            $existing->save();
        } else {
            Budget_Rapbs_Akun::create([
                'id_akun' => $validated['id_akun'],
                'id_unit' => $validated['id_unit'],
                'budget_rapbs_akun' => $validated['budget_rapbs_akun'], // fix di sini juga
            ]);
        }

        return back()->with('success', 'Data berhasil disimpan.');
    }



    public function importExcel(Request $request)
    {
        DB::statement("SET @current_user_id = " . auth()->id());

        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                if ($index === 1) continue; // header

                $kode_akun  = trim($row['A']);
                $akun_nama  = trim($row['B']);
                $budget     = (int) $row['C'];
                $kode_unit  = trim($row['D']);

                $akun = Akun::where('kode_akun', $kode_akun)->first();
                $unit = Unit::where('kode_unit', $kode_unit)->first();

                if (!$akun || !$unit) continue; // skip jika tidak ditemukan

                // Update or Insert
                Budget_Rapbs_Akun::updateOrCreate(
                    [
                        'id_akun' => $akun->id_akun,
                        'id_unit' => $unit->id_unit,
                    ],
                    ['budget_rapbs_akun' => $budget]
                );
            }

            DB::commit();
            return back()->with('success', 'Import Excel berhasil!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }


}
