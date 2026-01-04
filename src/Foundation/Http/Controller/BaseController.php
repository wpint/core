<?php 
namespace WPINT\Core\Foundation\Http\Controller;

use WPINT\Core\Foundation\Http\Handler;

abstract class BaseController
{

    /**
     * Middewares of the controller
     *
     * @var array
     */
    private $middleware = [];

    /**
     * Compact all the middlewares
     *
     * @param array $middleware
     * @return void
     */
    public function middleware(array $middleware = []) : self
    {
        for ($i=0; $i < count($middleware); $i++) { 
            $this->middleware[] = $middleware[$i];
        }
        return $this;
    }

    /**
     * Get The middlewares
     *
     * @return array
     */
    public function getMiddleware() : array
    {
        if(!$this->middleware) return [];
        return $this->middleware;
    }

    /**
     * Execute all the middlewares before going forward to the specified method
     *
     * @param [type] $closure
     * @param array $params
     * @return Mixed
     */
    public function callAction($closure, array $routeMiddlewares = [], array $params = []) : mixed
    { 
        $class = get_called_class();
        $middlewares = array_merge($class::getMiddleware(), $routeMiddlewares);
        $final = function($request) use ($class, $closure, $params) {
            return  app()->call("$class@$closure", $params);
        };
        return Handler::evaluate($middlewares, $final);
    }


}