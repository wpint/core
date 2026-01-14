<?php 
namespace WPINT\Core\Foundation;

use Illuminate\Support\ServiceProvider as SupportServiceProvider;
use WPINT\Core\Foundation\Support\DefaultProviders;

class ServiceProvider extends SupportServiceProvider
{

    /**
     * Get the default providers for a Laravel application.
     *
     * @return \Illuminate\Support\DefaultProviders
     */
    public static function defaultProviders()
    {
        return new DefaultProviders();
    }
    
}