<?php

namespace WPINT\Core\Foundation\Configuration;

use Illuminate\Foundation\Configuration\ApplicationBuilder as ConfigurationApplicationBuilder;
use Illuminate\Foundation\Configuration\Exceptions;

class ApplicationBuilder extends ConfigurationApplicationBuilder
{


    /**
     * Register the standard kernel classes for the application.
     *
     * @return $this
     */
    public function withKernels()
    {

        $this->app->singleton(
            \Illuminate\Contracts\Http\Kernel::class,
            \WPINT\Core\Foundation\Http\Kernel::class,
        );
        
        $this->app->singleton(
            \Illuminate\Contracts\Console\Kernel::class,
            \WPINT\Core\Foundation\Console\Kernel::class,
        );

        return $this;
    }

    /**
     * Register and configure the application's exception handler.
     *
     * @param  callable(\Illuminate\Foundation\Configuration\Exceptions)|null  $using
     * @return $this
     */
    public function withExceptions(?callable $using = null)
    {
        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \WPINT\Core\Foundation\Exceptions\Handler::class
        );

        if ($using !== null) {
            $this->app->afterResolving(
                \WPINT\Core\Foundation\Exceptions\Handler::class,
                fn ($handler) => $using(new Exceptions($handler)),
            );
        }

        return $this;
    }

    /**
     * Get the application instance.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function create()
    {   
        do_action("wpint_initialized", $this->app);
        return $this->app;
    }
}

