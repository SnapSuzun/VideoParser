<?php

namespace core;

/**
 * Class AutoLoader
 */
class AutoLoader
{
    /**
     * @var array
     */
    protected static $classMap = [];

    /**
     *
     */
    public static function register()
    {
        spl_autoload_register([self::class, 'autoload']);
    }

    /**
     * @param string $class
     */
    protected static function autoload($class)
    {
        if (!isset(self::$classMap[$class])) {
            $path = str_replace('\\', '/', $class) . ".php";
            if (!is_file($path)) {
                return;
            }
            self::$classMap[$class] = $path;
        }
        require_once(self::$classMap[$class]);
    }
}
