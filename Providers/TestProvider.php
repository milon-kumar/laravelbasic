<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class TestProvider extends ServiceProvider implements MyInterfaceClass
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Connection::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer("testfile", function (){
            return "ok";
        });
    }

    function myFunction()
    {
        // TODO: Implement myFunction() method.
    }
}
