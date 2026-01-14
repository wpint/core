<?php

namespace WPINT\Core\Foundation\Http;

use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Routing\Pipeline;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Request;
use Throwable;

class Kernel extends HttpKernel
{
    

    /**
     * Handle an incoming HTTP request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handle($request)
    {

        $this->requestStartedAt = Carbon::now();

        try {        
            $request->enableHttpMethodParameterOverride();   
            
            $response = $this->sendRequestThroughRouter($request);
            
        } catch (Throwable $e) {
            
            $this->reportException($e);
            
            $response = $this->renderException($request, $e);
            
        }
        

        $this->app['events']->dispatch(
            new RequestHandled($request, $response)
        );
    
        if($response)  return $response;
    }

    /**
     * Send the given request through the middleware / router.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendRequestThroughRouter($request)
    {
        $this->app->instance('request', $request);
        Request::clearResolvedInstance();

        $this->bootstrap();
   
        return (new Pipeline($this->app))
            ->send($request)
            ->through($this->app->shouldSkipMiddleware() ? [] : $this->middleware)
            ->then($this->dispatchToRouter());
    }

    /**
     * Get the route dispatcher callback.
     *
     * @return \Closure
     */
    protected function dispatchToRouter()
    {   

        return function ($request) {
            $this->app->instance('request', $request);
            return $this->router->dispatch($request);
        };

    }

}
