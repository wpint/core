<?php

namespace WPINT\Core\Foundation\Routing;

use Illuminate\Http\Request;
use Illuminate\Routing\RouteCollection as RoutingRouteCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RouteCollection extends RoutingRouteCollection
{

    /**
     * Find the first route matching a given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Routing\Route
     *
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function match(Request $request)
    {   
        $routes = $this->get($request->getMethod());
      
        // First, we will see if we can find a matching route for this current request
        // method. If we can, great, we can just return it so that it can be called
        // by the consumer. Otherwise we will check for routes with another verb.
        $route = $this->matchAgainstRoutes($routes, $request);
      
        return $this->handleMatchedRoute($request, $route);
    }

    /**
     * Determine if a route in the array matches the request.
     *
     * @param  \Illuminate\Routing\Route[]  $routes
     * @param  \Illuminate\Http\Request  $request
     * @param  bool  $includingMethod
     * @return \Illuminate\Routing\Route|null
     */
    protected function matchAgainstRoutes(array $routes, $request, $includingMethod = true)
    {

        $fallbackRoute = null;

        foreach ($routes as $route) {
            if ($route->matches($request, $includingMethod)) {
                if ($route->isFallback) {
                    $fallbackRoute ??= $route;

                    continue;
                }

                return $route;
            }
        }

        return $fallbackRoute;
    }

    /**
     * Handle the matched route.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Routing\Route|null  $route
     * @return \Illuminate\Routing\Route
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function handleMatchedRoute(Request $request, $route)
    {

        if (! is_null($route)) {
            return $route->bind($request);
        }
   
        // If no route was found we will now check if a matching route is specified by
        // another HTTP verb. If it is we will need to throw a MethodNotAllowed and
        // inform the user agent of which HTTP verb it should use for this route.
        $others = $this->checkForAlternateVerbs($request);

        if (count($others) > 0) {
            return $this->getRouteForMethods($request, $others);
        }
        

        throw new NotFoundHttpException(sprintf(
            'The route %s could not be found.',
            $request->path()
        ));
    }

}
