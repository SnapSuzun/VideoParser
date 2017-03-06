<?php

namespace models;

/**
 * Class Rutube
 * @package models
 */
class Rutube extends VideoService
{
    /**
     * @var array
     */
    public static $availableHosts = [
        'rutube.ru',
    ];

    /**
     * @return array
     * @throws \Exception
     */
    public function getInformation()
    {
        if (!preg_match('/video\/([^\/]+)/', $this->path, $matches)) {
            throw new \Exception('The argument with id of video is not found');
        }

        $videoId = $matches[1];

        $result = $this->sendQuery("http://rutube.ru/api/video/{$videoId}", ['format' => 'json']);

        if ($result['status'] != 'success') {
            throw new \Exception('Video service is not available now. Please, try again later.');
        }

        $videoData = json_decode($result['response'], true);

        $videoInfo = [
            'title' => $videoData['title'],
            'description' => $videoData['description'],
            'thumbnails' => ['default' => ['url' => $videoData['thumbnail_url']]],
            'html' => $videoData['html'],
        ];

        return $videoInfo;
    }
}