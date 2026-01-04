<?php

namespace WPINT\Core\Providers;

use WPINT\Framework\ServiceProvider;
use WPINT\Framework\Foundation\Vite;

class ViteServiceProvider extends ServiceProvider
{

    /**
     * Register method
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('vite', function (){
            return new Vite();
        });
        
    }

    /**
     * Boot method
     *
     * @return void
     */
    public function boot()
    {
        app()->make('vite');
    }
    
}