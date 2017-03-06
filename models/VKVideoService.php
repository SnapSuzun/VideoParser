<?php

namespace models;

/**
 * Class VKVideoService
 * @package models
 */
class VKVideoService extends VideoService
{
    /**
     * @var array
     */
    protected static $availableHosts = [
        'vk.com',
    ];

    public function getInformation()
    {
        if (!preg_match('/video([\-0-9_]+)/', $this->path, $matches)) {
            throw new \Exception('The argument with id of video is not found');
        }

        $videoId = $matches[1];
        
    }
}