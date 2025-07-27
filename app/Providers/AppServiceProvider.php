<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Notification;
use Illuminate\Pagination\Paginator;
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
     Paginator::useBootstrapFive();
    View::composer('*', function ($view) {
    if (auth()->check()) {
        $notifs = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $view->with('notifications', $notifs);
    } else {
        $view->with('notifications', collect());
    }
});
}


}
