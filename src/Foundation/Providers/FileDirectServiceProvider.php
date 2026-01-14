<?php 

namespace WPINT\Core\Foundation\Providers;

use WP_Filesystem_Direct;
use WPINT\Core\Foundation\Application;
use WPINT\Core\Foundation\ServiceProvider;

class FileDirectServiceProvider extends ServiceProvider
{
   
    /**
     * Register wp filesystem service.
     *
     * @return void
     */
    public function register() : void 
    {
        if( !class_exists( 'WP_Filesystem_Direct' ) ) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
            require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
        }

        $this->app->singleton('wp.file.direct', function(Application $app){
            return  new WP_Filesystem_Direct([]);
        });
    }

}