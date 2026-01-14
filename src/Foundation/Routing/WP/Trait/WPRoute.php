<?php 
namespace WPINT\Core\Foundation\Routing\WP\Trait;

use Illuminate\Routing\Pipeline;

trait WPRoute
{

    /**
     * Run Route Through the stack
     *
     * @return void
     */
    protected function runRoute()
    {
        $req = $this->container->make('request');
        $this->bind($req);                        
        $middleware = $this->gatherMiddleware();

        return (new Pipeline($this->container))
        ->send($req)
        ->through($middleware)
        ->then(function(){
            return $this->run();
        });
    }
    
}