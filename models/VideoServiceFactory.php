<?php

namespace models;

/**
 * Class VideoServiceFactory
 * @package models
 */
class VideoServiceFactory
{
    /**
     * @param $url
     * @return null|VideoService
     */
    public static function create($url)
    {
        $urlInfo = parse_url($url);
        if (!isset($urlInfo['host'])) {
            return null;
        }
        foreach (glob(__DIR__ . DIRECTORY_SEPARATOR . '*.php') as $file) {
            $className = basename($file, '.php');

            $className = __NAMESPACE__ . '\\' . $className;

            if (class_exists($className) && in_array(VideoService::class, class_parents($className)) && $className::availableHost($urlInfo['host'])) {
                return new $className($url);
            }
        }
        return null;
    }
}