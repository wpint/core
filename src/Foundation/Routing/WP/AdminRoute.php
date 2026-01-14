<?php 
namespace WPINT\Core\Foundation\Routing\WP;

use Illuminate\Routing\Route;
use WPINT\Core\Foundation\Routing\WP\Trait\WPRoute;
use Wpint\Route\Enums\RouteScopeEnum;

class AdminRoute extends Route
{
    use WPRoute;

    /**
     * Page's title 
     *
     * @var string
     */
    private string $pageTitle = 'Page Title';

    /**
     * Admin's menu title
     *
     * @var string
     */
    private string $menuTitle = 'Menu Title';

    /**
     * Admin page access capability
     *
     * @var string
     */
    private string $capability = 'manage_options';
    
    /**
     * Admin page menu's icon 
     *
     * @var string
     */
    private string $icon = '';

    /**
     * $position
     *
     * @var integer|null
     */
    private int|null $position = null;

    /**
     * The parent of this page
     *
     * @var string|null
     */
    private string|null $parent = null;

    /**
     * is Rendered
     *
     * @var boolean
     */
    private static $rendered = false;
    
    public function __construct($slug, $action, $container, $router)
    {
        parent::__construct("GET", $slug, $action);
        $this->setRouter($router);
        $this->setContainer($container);
    }

    /**
     * Register the page
     *
     * @return void
     */
    public function register()
    {
        add_action('admin_menu', [$this, 'wpRegisterAdminRoute']);
    }
    
    /**
     * Route scope
     *
     * @return RouteScopeEnum
     */
    public static function scope() : RouteScopeEnum
    {
        return RouteScopeEnum::ADMIN;
    }

    /**
     * set Parent of page
     *
     * @param string $parent
     * @return self
     */
    public function parent(string $parent) : self
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * set capability of page
     *
     * @param string $capability
     * @return self
     */
    public function capability(string $capability) : self
    {
        $this->capability = $capability;
        return $this;
    }

    /**
     * set menuTitle of page
     *
     * @param string $menuTitle
     * @return self
     */
    public function menuTitle(string $menuTitle) : self
    {
        $this->menuTitle = $menuTitle;
        return $this;
    }

    /**
     * set pageTitle of page
     *
     * @param string $pageTitle
     * @return self
     */
    public function pageTitle(string $pageTitle) : self
    {
        $this->pageTitle = $pageTitle;
        return $this;
    }

    /**
     * set page icon
     *
     * @param string $icon
     * @return self
     */
    public function icon(string $icon) : self
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * set position of page menu
     *
     * @param integer $position
     * @return self
     */
    public function position(int $position) : self
    {
        $this->position = $position;
        return $this;  
    }

    /**
     * execution of wp function: add_menu_page|add_submenu_page 
     *
     * @return void
     */
    public function wpRegisterAdminRoute()
    {

        if(!$this->parent){
            add_menu_page(
                __( $this->uri, 'wpint_framework' ),
                __( $this->uri, 'wpint_framework' ),
                $this->capability,
                $this->uri,
                function() {
                    if( !self::$rendered ){
                        return $this->runRoute();
                    }
                },
                $this->icon,
                $this->position
            );
        }
        else
        {
            add_submenu_page(
               $this->parent ?? $this->uri, 
                __( $this->pageTitle ?? $this->uri, 'wpint_framework' ),
                __( $this->menuTitle ?? $this->uri, 'wpint_framework' ),
                $this->capability,
                $this->uri, 
                function() {
                    if( !self::$rendered ){
                        return $this->runRoute();
                    }
                }, 
                $this->position
            );
        }
    }
    


}