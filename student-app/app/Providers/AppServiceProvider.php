<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

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
    public function boot(): void
    {
        Carbon::macro('isCurrentQuarter', function () {
            $now = Carbon::now();
            $currentQuarterStart = Carbon::create($now->year, floor(($now->month - 1) / 3) * 3 + 1, 1)->startOfDay();
            $currentQuarterEnd = $currentQuarterStart->copy()->addMonths(3)->endOfDay();
            
            return $this->between($currentQuarterStart, $currentQuarterEnd);
        });
    }
}
