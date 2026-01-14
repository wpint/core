<?php 
namespace WPINT\Core\Foundation\Routing\WP;

use Wpint\Route\Enums\RouteScopeEnum;
use Illuminate\Routing\Route;
use WPINT\Core\Foundation\Routing\WP\Trait\WPRoute;

class AjaxRoute extends Route
{
    use WPRoute;
    
    /**
     * route's security
     *
     * @var [type]
     */
    private $private = false;

    public function __construct($slug, $action, $container, $router)
    {
        parent::__construct("POST", $slug, $action);
        $this->setRouter($router);
        $this->setContainer($container);
    }

    /**
     * Register the ajax route
     *
     * @return void
     */
    public function register()
    {
        if($this->private)
        {
            add_action( "wp_ajax_{$this->uri()}", [$this, 'wpResgisterAjaxRoute'] );
        } else {
            add_action( "wp_ajax_{$this->uri()}", [$this, 'wpResgisterAjaxRoute'] );
            add_action( "wp_ajax_nopriv_{$this->uri()}", [$this, 'wpResgisterAjaxRoute'] );
        }

    }
    
    /**
     * Route scope
     *
     * @return RouteScopeEnum
     */
    public static function scope() : RouteScopeEnum
    {
        return RouteScopeEnum::AJAX;
    }

    /**
     * Route's security
     *
     * @return self
     */
    public function private() : self 
    {
        $this->private = true;
        return $this;
    }

    /**
     * register & call the controller's  ajax callback
     *
     * @return void
     */
    public function wpResgisterAjaxRoute()
    {
        return  $this->runRoute();
    }

}