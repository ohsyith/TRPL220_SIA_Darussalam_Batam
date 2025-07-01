<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Kegiatan;
use App\Models\Akuntan_Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Budget_Rapbs_Kegiatan;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BudgetRapbsKegiatanController extends Controller
{
    
    public function index(Request $request)
    {
        $user = Auth::user();

        // Penentuan unit dan daftar unit berdasarkan role
        if (in_array($user->role, ['admin', 'auditor'])) {
            $id_unit = $request->get('unit', 'all');
            $units = Unit::all();
        } elseif ($user->role === 'akuntan_unit') {
            $akuntanUnit = Akuntan_Unit::where('id_akuntan_unit', $user->id_user)->firstOrFail();
            $id_unit = $akuntanUnit->id_unit;
            $units = collect(); // kosongkan agar tidak render <select>
        } else {
            abort(403, 'Role tidak dikenal');
        }

        // Ambil data kegiatan
        if ($id_unit === 'all') {
            $kegiatan = Kegiatan::select(
                    'kegiatan.id_kegiatan',
                    'kegiatan.kode_kegiatan',
                    'kegiatan.kegiatan',
                    DB::raw('SUM(budget_rapbs_kegiatan.budget_rapbs_kegiatan) as budget_rapbs')
                )
                ->leftJoin('budget_rapbs_kegiatan', 'kegiatan.id_kegiatan', '=', 'budget_rapbs_kegiatan.id_kegiatan')
                ->groupBy('kegiatan.id_kegiatan', 'kegiatan.kode_kegiatan', 'kegiatan.kegiatan')
                ->orderBy('kegiatan.kode_kegiatan')
                ->get();
        } else {
            $kegiatan = Kegiatan::select(
                    'kegiatan.id_kegiatan',
                    'kegiatan.kode_kegiatan',
                    'kegiatan.kegiatan',
                    DB::raw('COALESCE(budget_rapbs_kegiatan.budget_rapbs_kegiatan, 0) as budget_rapbs')
                )
                ->leftJoin('budget_rapbs_kegiatan', function ($join) use ($id_unit) {
                    $join->on('kegiatan.id_kegiatan', '=', 'budget_rapbs_kegiatan.id_kegiatan')
                        ->where('budget_rapbs_kegiatan.id_unit', $id_unit);
                })
                ->orderBy('kegiatan.kode_kegiatan')
                ->get();
        }

        return view('budget-rapbs-kegiatan', compact('kegiatan', 'units', 'id_unit', 'user'));
    }



    public function storeOrUpdate(Request $request)
    {
        DB::statement("SET @current_user_id = " . auth()->id());

        $validated = $request->validate([
            'id_kegiatan' => 'required|integer',
            'id_unit' => 'required|integer',
            'budget_rapbs_kegiatan' => 'required|numeric',
        ]);

        Budget_Rapbs_Kegiatan::updateOrCreate(
            [
                'id_kegiatan' => $validated['id_kegiatan'],
                'id_unit' => $validated['id_unit'],
            ],
            [
                'budget_rapbs_kegiatan' => $validated['budget_rapbs_kegiatan']
            ]
        );

        return back()->with('success', 'Budget RAPBS kegiatan berhasil disimpan.');
    }


    

    public function importExcel(Request $request)
    {
        DB::statement("SET @current_user_id = " . auth()->id());

        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // Mapping kegiatan dan unit (hindari query dalam loop)
        $kegiatanMap = Kegiatan::pluck('id_kegiatan', 'kode_kegiatan');
        $unitMap = Unit::pluck('id_unit', 'kode_unit');

        try {
            DB::transaction(function () use ($rows, $kegiatanMap, $unitMap) {
                foreach ($rows as $index => $row) {
                    if ($index === 1) continue; // skip header

                    $kode_kegiatan = trim($row['A']);
                    $budget = (int) $row['C'];
                    $kode_unit = trim($row['D']);

                    $id_kegiatan = $kegiatanMap[$kode_kegiatan] ?? null;
                    $id_unit = $unitMap[$kode_unit] ?? null;

                    if (!$id_kegiatan || !$id_unit) continue;

                    Budget_Rapbs_Kegiatan::updateOrCreate(
                        [
                            'id_kegiatan' => $id_kegiatan,
                            'id_unit' => $id_unit,
                        ],
                        [
                            'budget_rapbs_kegiatan' => $budget
                        ]
                    );
                }
            });

            return back()->with('success', 'Import Excel RAPBS kegiatan berhasil!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }




}
