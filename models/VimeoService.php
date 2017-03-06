<?php

namespace models;

/**
 * Class VimeoService
 * @package models
 */
class VimeoService extends VideoService
{
    /**
     * @var array
     */
    protected static $availableHosts = [
        'vimeo.com',
        'player.vimeo.com',
    ];


    /**
     * @return array
     * @throws \Exception
     */
    public function getInformation()
    {
        $scheme = parse_url($this->url, PHP_URL_SCHEME);

        if (empty($scheme)) {
            $this->url = '//' . $this->url;
        }

        $result = $this->sendQuery('https://vimeo.com/api/oembed.json', ['url' => $this->url]);
        if ($result['status'] != 'success') {
            if ($result['code'] == 404) {
                throw new \Exception('Video is not found.');
            }
            throw new \Exception('Video service is not available now. Please, try again later.');
        }

        $videoData = json_decode($result['response'], true);

        $videoInfo = [
            'title' => $videoData['title'],
            'description' => $videoData['description'],
            'thumbnails' => [
                'default' => [
                    'url' => $videoData['thumbnail_url'],
                    'width' => $videoData['thumbnail_width'],
                    'height' => $videoData['thumbnail_height']
                ]
            ],
            'html' => $videoData['html'],
        ];
        return $videoInfo;
    }
}