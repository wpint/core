<?php 
namespace WPINT\Core\Foundation\Routing;

use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Events\Routing;
use Illuminate\Routing\Pipeline;
use Illuminate\Routing\Router as RoutingRouter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Throwable;
use WPINT\Core\Foundation\Exceptions\PreventDispatchException;
use WPINT\Core\Foundation\Routing\WP\AdminRoute;
use WPINT\Core\Foundation\Routing\WP\AjaxRoute;
use WPINT\Core\Foundation\Routing\WP\RestRoute;

class Router extends RoutingRouter
{

    public function __construct(Dispatcher $events, ?Container $container = null)
    {   
        $this->events = $events;
        $this->routes = new RouteCollection;
        $this->container = $container ?: new Container;
    }

    /**
     * Dispatch the request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dispatch(Request $request)
    {   
        
        $this->currentRequest = $request;
        
        if(!$this->shouldDispatch($request)) return;

        return $this->dispatchToRoute($request);
    }

    /**
     * Dispatch the request to a route and return the response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dispatchToRoute(Request $request)
    {   
        $route = $this->findRoute($request);

        if($route)
            return $this->runRoute($request, $route);
 
        return false; 
    }


    /**
     * Find the route matching a given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Routing\Route
     */ 
    protected function findRoute($request)
    {
        try{
            
            $this->events->dispatch(new Routing($request));
                
            $this->current = $route = $this->routes->match($request);
   
            $route->setContainer($this->container);
            $this->container->instance(Route::class, $route);
            
            return $route;

        }catch(Throwable $th)
        {
            return false;
        }
    }

    /**
     * WP ADMIN Route
     *
     * @param [type] $path
     * @param [type] $action
     * @return \WPINT\Core\Foundation\Routing\WP\AdminRoute;
     */
    public function admin($path, $action = null)
    {
        return new AdminRoute($path, $action, $this->container, $this);
    }

    /**
     * WP AJAX Route
     *
     * @param [type] $path
     * @param [type] $action
    * @return \WPINT\Core\Foundation\Routing\WP\AjaxRoute;
     */
    public function ajax($path, $action = null)
    {
        return new AjaxRoute($path, $action, $this->container, $this);
    }

    /**
     * WP REST Route
     *
     * @param [type] $path
     * @param [type] $action
     * @return \WPINT\Core\Foundation\Routing\WP\RestRoute;
     */
    public function rest($method, $path, $action = null)
    {
        return new RestRoute($method, $path, $action, $this->container, $this);
    }

    /**
     * Run through prevent dispatch functions 
     *
     * @return boolean
     */
    public function shouldDispatch(Request $request)
    {       

     

        $url = $request->url();
        $pipes =  apply_filters('wpint_prevent_dispatch', [
            'login_url'     =>      fn()    =>  Str::doesntContain($url, Str::rtrim(wp_login_url(), '/')),
            'admin_url'     =>      fn()    =>  Str::doesntContain($url, Str::rtrim(admin_url(), '/')),
            'content_url'   =>      fn()    =>  Str::doesntContain($url, WP_CONTENT_URL),
            'rest_url'      =>      fn()    =>  Str::doesntContain($url, rest_get_url_prefix()) 
        ]);
        
        try{
            collect($pipes)->each(function($pipe, $key){
                if(!$pipe()) throw new PreventDispatchException("WP Prevents to dispatch $key route.") ; 
            });

            return true;
        }catch(PreventDispatchException $e)
        {   

            return false;
        }catch(Throwable $th)
        {
             
            throw $th;
        }

        
    }

}