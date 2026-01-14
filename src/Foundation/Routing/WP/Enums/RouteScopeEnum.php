<?php 
namespace WPINT\Core\Foundation\Routing\WP\Enum;

enum RouteScopeEnum
{

    case ADMIN;
    case WEB;
    case REST;
    case AJAX;

}