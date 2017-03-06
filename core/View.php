<?php

namespace core;

use Core;

/**
 * Class View
 * @package core
 */
class View
{
    /**
     * @var string
     */
    public $title = '';
    /**
     * @var array
     */
    protected $_possibleExtensions = [];

    /**
     * @var string
     */
    public $defaultExtension = 'php';

    /**
     * @var null | string
     */
    protected $_path = null;

    /**
     * @var null | Controller
     */
    public $context = null;

    /**
     * @var array
     */
    protected $_viewFiles = [];

    /**
     * @param string $view
     * @param array $params
     * @param Controller $context
     * @throws \Exception
     * @return string
     */
    public function render($view, $params = [], $context = null)
    {
        $oldContext = $this->context;
        if (!empty($context)) {
            $this->context = $context;
        }
        $file = $this->findViewFile($view, $this->context);

        if (empty($file)) {
            throw new \Exception("View $view not found", 404);
        }

        $this->_viewFiles[] = $file;

        $content = $this->renderFile($file, $params);

        array_pop($this->_viewFiles);
        $this->context = $oldContext;

        return $content;
    }

    /**
     * @param string $view
     * @param null | Controller $context
     * @return string
     */
    protected function findViewPath($view, $context = null)
    {
        $path = Core::$app->getViewPath();
        if (strncmp($view, '//', 2) === 0) {
            $path .= DIRECTORY_SEPARATOR . trim($view, '/');
            return trim($path, '/');
        } elseif (!empty($context)) {
            $path = trim($context->getViewPath(), '/');
        }

        $path .= DIRECTORY_SEPARATOR . trim($view, '/');

        return trim($path, '/');
    }

    /**
     * @param string $view
     * @param null | Controller $context
     * @return string
     */
    public function findViewFile($view, $context = null)
    {
        $file = $this->findViewPath($view, $context);

        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
            return $file;
        }

        $path = "$file.$this->defaultExtension";
        if (file_exists($path)) {
            return $path;
        }

        foreach ($this->_possibleExtensions as $ext) {
            $path = "$file.$ext";
            if (file_exists($path)) {
                return $path;
            }
        }

        return '';
    }

    /**
     * @param string $file
     * @param array $params
     * @return string
     */
    protected function renderFile($file, $params)
    {
        ob_start();
        ob_implicit_flush(false);
        extract($params, EXTR_OVERWRITE);
        require($file);


        return ob_get_clean();
    }
}
