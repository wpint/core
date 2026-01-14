<?php 
namespace WPINT\Core\Foundation\Routing;


enum RouteTypeEnum
{

    case AdminRoute;
    case WebRoute;
    case RestRoute;
    case AjaxRoute;

}