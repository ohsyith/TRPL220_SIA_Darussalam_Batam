<?php

namespace App\Http\Controllers;

use App\Models\Log_Activity;
use Illuminate\Http\Request;

class LogActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Log_Activity::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('user', function ($query) use ($request) {
                    $query->where('username', 'like', '%' . $request->search . '%');
                })
                ->orWhere('keterangan', 'like', '%' . $request->search . '%') // Mencari di kolom keterangan
                ->orWhereDate('created_at', 'like', '%' . $request->search . '%'); // Mencari di kolom created_at
            });
        }
        
        // Filter berdasarkan tanggal jika ada
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00', // Mulai dari awal hari
                $request->end_date . ' 23:59:59',   // Hingga akhir hari
            ]);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startTime = $request->start_time ?? '00:00:00';
            $endTime = $request->end_time ?? '23:59:59';
        
            $query->whereBetween('created_at', [
                $request->start_date . ' ' . $startTime,
                $request->end_date . ' ' . $endTime,
            ]);
        }
        
        
        

        // Ambil log aktivitas yang sudah difilter
        $log_aktivitas = $query->get();

        return view('admin.log-aktivitas', compact('log_aktivitas'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Log_Activity $log_Activity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Log_Activity $log_Activity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Log_Activity $log_Activity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Log_Activity $log_Activity)
    {
        //
    }
}
