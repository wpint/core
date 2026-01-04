<?php 
namespace WPINT\Core\Providers;

use WPINT\Core\Foundation\Application;
use WPINT\Core\Foundation\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
   
    /**
     * Register application Service.
     *
     * @return void
     */
    public function register() : void 
    {
        $this->app->instance('app', function(Application $app){
            return $app::getInstance();
        });
    }

    public function boot() : void
    {

    }

}