<?php 
namespace WPINT\Core\Foundation\Routing\WP\Enum;
use Illuminate\Support\Str;

enum RouteHttpMethodEnum : string
{

    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case PATCH = 'PATCH';
    case DELETE = 'DELETE';
    case ANY = 'ANY';


    /**
     * Get Lowercase of the method
     *
     * @param self $method
     * @return void
     */
    public function lower()
    {
        return match($this) {
            RouteHttpMethodEnum::GET =>  Str::lower(RouteHttpMethodEnum::GET->name),
            RouteHttpMethodEnum::POST =>  Str::lower(RouteHttpMethodEnum::POST->name),
            RouteHttpMethodEnum::PUT =>  Str::lower(RouteHttpMethodEnum::PUT->name),
            RouteHttpMethodEnum::PATCH =>  Str::lower(RouteHttpMethodEnum::PATCH->name),
            RouteHttpMethodEnum::DELETE =>  Str::lower(RouteHttpMethodEnum::DELETE->name),
            RouteHttpMethodEnum::ANY =>  Str::lower(RouteHttpMethodEnum::ANY->name)
        };
    }

}