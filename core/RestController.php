<?php

namespace core;

/**
 * Class RestController
 * @package core
 */
class RestController extends Controller
{
    const RESPONSE_FORMAT_JSON = 'json';
    const RESPONSE_FORMAT_RAW = 'raw';

    /**
     * @var string
     */
    protected $responseFormat = self::RESPONSE_FORMAT_JSON;

    /**
     * @param \Throwable $exception
     * @return string
     */
    protected function exceptionHandler($exception)
    {
        return $this->buildResponse(400, [
            'status' => 'fail',
            'error' => $exception->getMessage()
        ]);
    }

    /**
     * @param string $message
     * @return string
     */
    protected function fail($message)
    {
        return $this->buildResponse(400, ['status' => 'fail', 'error' => $message]);
    }

    /**
     * @param array $data
     * @return string
     */
    protected function success($data)
    {
        return $this->buildResponse(200, ['status' => 'success', 'data' => $data]);
    }

    /**
     * @param $code
     * @param $data
     * @return string
     */
    protected function buildResponse($code, $data)
    {
        http_response_code($code);
        switch ($this->responseFormat) {
            case self::RESPONSE_FORMAT_JSON: {
                $data = json_encode($data);
                break;
            }
        }
        return $data;
    }

    /**
     * @param mixed $result
     * @return mixed|string
     */
    protected function afterRunAction($result)
    {
        if (!is_string($result)) {
            $result = $this->success($result);
        }
        return $result;
    }
}