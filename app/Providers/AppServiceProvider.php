<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\Hak_Akses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $hak_akses = null;

                // Hanya cek hak akses untuk akuntan_unit, karena akuntan_divisi bisa semua
                if ($user->role === 'akuntan_unit') {
                    $hak_akses = Hak_Akses::where('id_akuntan_unit', $user->id_user)->first();
                }

            $view->with('user', $user)
                    ->with('sidebarRole', $user->role)
                    ->with('sidebarHakAkses', $hak_akses);
            }
        });




        Carbon::setLocale('id');

    }



}
