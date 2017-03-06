<?php

namespace models;

/**
 * Interface VideoService
 * @package models
 */
abstract class VideoService
{
    const QUERY_METHOD_GET = 'get';
    const QUERY_METHOD_POST = 'post';

    /**
     * @var array
     */
    protected static $availableHosts = [];

    /**
     * @var string
     */
    protected $url = '';

    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * @var string
     */
    protected $path = '';

    /**
     * @var string
     */
    protected $host = '';

    /**
     * VideoService constructor.
     * @param $url
     */
    public function __construct($url)
    {
        $this->url = $url;
        $urlInfo = parse_url($this->url, PHP_URL_QUERY);
        $this->path = trim(parse_url($this->url, PHP_URL_PATH), '/');
        $this->host = parse_url($this->url, PHP_URL_HOST);
        $this->host = preg_replace("/^www./", "", $this->host);

        if (!empty($urlInfo)) {
            parse_str($urlInfo, $this->arguments);
        }
    }

    /**
     * @return array | bool
     */
    public abstract function getInformation();

    /**
     * @param string $host
     * @return bool
     */
    public static function availableHost($host)
    {
        $trimmedHost = preg_replace("/^www./", "", $host);
        return in_array($trimmedHost, static::$availableHosts);
    }

    /**
     * @param string $url
     * @param integer $width
     * @param integer $height
     * @return string
     */
    protected function buildFrame($url, $width = 640, $height = 480)
    {
        return "<iframe src=\"{$url}\" width=\"{$width}\" height=\"{$height}\" frameborder=\"0\" allowfullscreen></iframe>";
    }

    /**
     * @param string $url
     * @param array $params
     * @param string $method
     * @param array $headers
     * @return array
     */
    protected function sendQuery($url, $params = [], $method = self::QUERY_METHOD_GET, $headers = [])
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        if ($method == self::QUERY_METHOD_POST) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        } else {
            $url = trim($url, '?') . '?' . http_build_query($params);
            curl_setopt($curl, CURLOPT_URL, $url);
        }

        if (!empty($headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($curl);
        $info = curl_getinfo($curl);

        $result = [
            'status' => $info['http_code'] == 200 ? 'success' : 'fail',
            'response' => $response,
            'code' => $info['http_code'],
        ];
        return $result;
    }
}
