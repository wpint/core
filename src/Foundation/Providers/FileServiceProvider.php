<?php 

namespace WPINT\Core\Foundation\Providers;

use WP_Filesystem_Base;
use WPINT\Core\Foundation\ServiceProvider;
use WPINT\Framework\Foundation\Application;

class FileServiceProvider extends ServiceProvider
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
        }


        $this->app->singleton('wp.file.base', function(Application $app){
            return  new WP_Filesystem_Base();
        });
    }

}