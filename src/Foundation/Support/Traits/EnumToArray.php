<?php 
namespace Wpint\Foundation\Support\Traits;

trait EnumToArray
{

    /**
     * Get Names
     *
     * @return array
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get values
     *
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * To Array
     *
     * @return array
     */
    public static function toArray(): array
    {
        return array_combine(self::values(), self::names());
    }

}