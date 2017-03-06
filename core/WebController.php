<?php

namespace core;

use Core;
use Exception;

/**
 * Class WebController
 * @package core
 */
class WebController extends Controller
{
    /**
     * @var null | string
     */
    public $layout = null;

    /**
     * @var null | string
     */
    protected $_viewPath = null;

    /**
     * @var null | View
     */
    protected $_view = null;


    /**
     * WebController constructor.
     * @param string $id
     */
    public function __construct($id)
    {
        parent::__construct($id);
        if (Core::$app->layout !== null) {
            $this->layout = Core::$app->layout;
        }
    }

    /**
     * @param Exception $exception
     * @return string
     */
    public function actionNotFound($exception)
    {
        header("HTTP/1.0 404 Not Found");
        return $this->render('error', [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode()
        ]);
    }

    /**
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render($view, $params = [])
    {
        $content = $this->getView()->render($view, $params, $this);
        if ($this->layout !== null && !empty($this->getView()->findViewFile($this->layout))) {
            $content = $this->getView()->render($this->layout, ['content' => $content], $this);
        }
        return $content;
    }

    /**
     * @param string $view
     * @param array $params
     * @return string
     */
    public function renderView($view, $params = [])
    {
        $content = $this->getView()->render($view, $params, $this);
        return $content;
    }

    /**
     * @return \core\View|null
     */
    public function getView()
    {
        if ($this->_view === null) {
            $this->_view = new View();
        }
        return $this->_view;
    }

    /**
     * @param View $view
     */
    public function setView($view)
    {
        if ($view instanceof View) {
            $this->_view = $view;
        }
    }

    /**
     * @return null|string
     */
    public function getViewPath()
    {
        if ($this->_viewPath === null) {
            $this->_viewPath = Core::$app->getViewPath() . DIRECTORY_SEPARATOR . trim($this->id, '/');
        }

        return $this->_viewPath;
    }

    /**
     * @param string $path
     */
    public function setViewPath($path)
    {
        $this->_viewPath = trim($path, '/');
    }
}