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
        $videoId = null;
        $url = '';
        $params = [
            'format' => 'json',

        ];
        if (preg_match('/play\/embed\/([^\/]+)/', $this->path, $matches)) {
            $videoId = $matches[1];
            $url = "http://rutube.ru/api/oembed";
            $params['url'] = "http://rutube.ru/tracks/{$videoId}.html";
        } elseif (preg_match('/video\/([^\/]+)/', $this->path, $matches)) {
            $videoId = $matches[1];
            $url = "http://rutube.ru/api/video/{$videoId}";
        }

        if (is_null($videoId)) {
            throw new \Exception('The argument with id of video is not found');
        }

        $result = $this->sendQuery($url, $params);

        if ($result['status'] != 'success') {
            throw new \Exception('Video service is not available now. Please, try again later.');
        }

        $videoData = json_decode($result['response'], true);

        $videoInfo = [
            'title' => $videoData['title'],
            'thumbnails' => ['default' => ['url' => $videoData['thumbnail_url']]],
            'html' => $videoData['html'],
        ];

        if (isset($videoData['description'])) {
            $videoInfo['description'] = $videoData['description'];
        }

        if (isset($videoData['thumbnail_width'])) {
            $videoInfo['thumbnails']['default']['width'] = $videoData['thumbnail_width'];
        }
        if (isset($videoData['thumbnail_height'])) {
            $videoInfo['thumbnails']['default']['height'] = $videoData['thumbnail_height'];
        }
        return $videoInfo;
    }
}