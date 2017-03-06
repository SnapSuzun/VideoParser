<?php

namespace models;

/**
 * Class Youtube
 * @package models
 */
class Youtube extends VideoService
{
    const API_KEY = 'AIzaSyAC4fxwmwXOChd109ohiqbHAWe_ilVFw2A';

    /**
     * @var array
     */
    protected static $availableHosts = [
        'youtube.com',
        'youtu.be'
    ];

    /**
     * @return array
     * @throws \Exception
     */
    public function getInformation()
    {
        if ($this->host == 'youtu.be') {
            $this->arguments['v'] = trim($this->path, '/');
        }

        if (preg_match('*/embed/([^/]+)*', $this->path, $matches)) {
            $this->arguments['v'] = $matches[1];
        }

        if (!isset($this->arguments['v'])) {
            throw new \Exception('The argument with id of video is not found');
        }
        $result = $this->sendQuery('https://www.googleapis.com/youtube/v3/videos',
            ['id' => $this->arguments['v'], 'key' => self::API_KEY, 'part' => 'snippet']);

        if ($result['status'] != 'success') {
            throw new \Exception('Video service is not available now. Please, try again later.');
        }

        $data = json_decode($result['response'], true);

        if (empty($data) || !isset($data['items']) || empty($data['items'])) {
            throw new \Exception('Video at the specified url is not found');
        }

        $videoData = $data['items'][0];

        $videoInfo = [
            'title' => $videoData['snippet']['title'],
            'description' => $videoData['snippet']['description'],
            'thumbnails' => $videoData['snippet']['thumbnails'],
            'html' => $this->buildFrame("https://www.youtube.com/embed/{$videoData['id']}"),
        ];

        return $videoInfo;
    }
}
