<?php 
namespace WPINT\Core\Providers;

use Illuminate\Http\Request;
use WPINT\Core\Foundation\Application;
use WPINT\Core\Foundation\ServiceProvider;

class RequestServiceProvider extends ServiceProvider
{
   
    /**
     * Register request service.
     *
     * @return void
     */
    public function register() : void 
    {
        $this->app->singleton('request', function(Application $app){
            return Request::capture();
        });
    }

}