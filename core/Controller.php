<?php

namespace core;

use Core;
use Exception;

/**
 * Class Controller
 * @package core
 */
abstract class Controller
{
    /**
     * @var string
     */
    public $id = null;

    /**
     * @var array
     */
    protected $_actionMap = [];

    /**
     * Controller constructor.
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @param \Throwable $exception
     * @return string
     */
    public function actionNotFound($exception)
    {
        header("HTTP/1.0 404 Not Found");
        return 'Not found';
    }

    /**
     * @param \Throwable $exception
     * @return string
     */
    protected function exceptionHandler($exception)
    {
        http_response_code(400);
        return $exception->getMessage();
    }

    /**
     * @param $id
     * @return Controller
     * @throws Exception
     */
    public static function getController($id)
    {
        if (empty($id)) {
            throw new Exception("Empty controller name", 404);
        }
        $parts = explode('/', trim($id, ' /'));
        if (count($parts) > 0) {
            $parts[count($parts) - 1] = ucfirst($parts[count($parts) - 1]);
        }
        $controllerClass = str_replace('/', '\\', Core::$app->getControllerPath()) . '\\' . implode('\\',
                $parts) . 'Controller';
        if (!class_exists($controllerClass)) {
            throw new Exception("Controller not found!", 404);
        }

        $obj = new $controllerClass($id);
        return $obj;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function hasAction($id)
    {
        if (!isset($this->_actionMap[$id])) {
            $action_parts = explode("-", $id);
            $action_parts = array_map(function ($part) {
                return ucfirst($part);
            }, $action_parts);
            $actionName = 'action' . implode("", $action_parts);

            if (method_exists($this, $actionName)) {
                $this->_actionMap[$id] = $actionName;
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $id
     * @param array $args
     * @return mixed
     * @throws Exception
     */
    public function runAction($id, $args = [])
    {
        try {
            if (!$this->hasAction($id)) {
                throw new Exception("Action $id not exist", 404);
            }

            $action = $this->_actionMap[$id];

            $method = new \ReflectionMethod($this, $action);

            $methodParams = $method->getParameters();

            if (count($methodParams) > count($args)) {
                throw new Exception("Missing required parameter $" . $methodParams[count($args)], 400);
            }

            $result = call_user_func_array([$this, $action], $args);
            $result = $this->afterRunAction($result);
            return $result;
        } catch (\Throwable $e) {
            return $this->exceptionHandler($e);
        }
    }

    /**
     * @param mixed $result
     * @return mixed
     */
    protected function afterRunAction($result)
    {
        return $result;
    }
}
