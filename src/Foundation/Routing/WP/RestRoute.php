<?php 
namespace WPINT\Core\Foundation\Routing\WP;

use Closure;
use Illuminate\Routing\Route;
use WPINT\Core\Foundation\Routing\WP\Enum\RouteScopeEnum;
use WPINT\Core\Foundation\Routing\WP\Trait\WPRoute;

class RestRoute extends Route
{
    use WPRoute;
    
    /**
     * route's namespace
     *
     * @var string
     */
    private $namespace = '';

    /**
     * route's permission
     *
     * @var boolean
     */
    private $permission = true;


    public function __construct($methods, $slug, $action, $container, $router)
    {
        parent::__construct($methods, $slug, $action);
        $this->setRouter($router);
        $this->setContainer($container);

    }

    /**
     * Register the rest endpoint
     *
     * @return void
     */
    public function register()
    {  
        add_action('rest_api_init', [$this, 'wpRegisterRestEndpointCallback']);
    }

    /**
     * Route scope
     *
     * @return RouteScopeEnum
     */
    public static function scope() : RouteScopeEnum
    {
        return RouteScopeEnum::REST;
    }

    /**
     * endpoint's namespace
     *
     * @param string $namespace
     * @return self
     */
    public function namespace(string $namespace) : self
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * Get Namespace
     *
     * @return string|null
     */
    public function getNameSpace()
    {
        return $this->namespace;
    }

    /**
     * route's permission
     *
     * @param Closure|string|array $permission
     * @return self
     */
    public function permission(Closure $permission) : self
    {
        if(!$permission) $this->permission = true;
        $this->permission = $permission;
        return $this;
    }

    /**
     * Get Permission callback
     *
     * @return void
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * Rest Callback
     *
     * @return void
     */
    public function wpRegisterRestEndpointCallback()
    {
        register_rest_route(
                $this->getNameSpace(), 
                $this->uri(), 
                [
                'methods'  => $this->methods,
                'callback'  => function($data)
                {
                    return $this->runRoute();
                },
                'permission_callback'   => function()
                    {
                        if(is_callable($this->getPermission())) return app()->call($this->getPermission());
                        return $this->getPermission();
                    }
                ], 
                false
            );
        
    }


}