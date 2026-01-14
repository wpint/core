<?php 
namespace WPINT\Core\Foundation\Routing;

use Illuminate\Routing\RoutingServiceProvider;
use WPINT\Core\Foundation\Routing\Router;
use WPINT\Core\Foundation\Routing\WP\AdminRoute;

class RouterServiceProvider extends RoutingServiceProvider
{
   
        /**
        * Register the router instance.
        *
        * @return void
        */
        protected function registerRouter()
        {
                $this->app->singleton('router', function ($app) {
                    return new Router($app['events'], $app);
                });
        }



}