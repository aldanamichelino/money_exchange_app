<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App;
use App\Models\Account;

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
        App::singleton('user_account', function(){
            return Account::where('user_id', auth()->id())->first();
        });
    }
}
