<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('currency', function ($expression) {
            return "<?php 
                if (strpos((string) $expression, '.') !== false) {
                    // Hitung jumlah desimal jika ada
                    \$decimalCount = strlen(substr(strrchr((string) $expression, '.'), 1));
                    echo 'Rp ' . number_format($expression, \$decimalCount, ',', '.');
                } else {
                    // Jika tidak ada desimal, tampilkan tanpa desimal
                    echo 'Rp ' . number_format($expression, 0, ',', '.');
                }
            ?>";
        });
        
    }
}
