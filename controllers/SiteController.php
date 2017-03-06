<?php

namespace controllers;

use Core;
use core\RestController;
use models\VideoServiceFactory;

/**
 * Class SiteController
 * @package controllers
 */
class SiteController extends RestController
{
    /**
     * @return array|bool|string
     */
    public function actionIndex()
    {
        try {
            $service = VideoServiceFactory::create('https://youtu.be/y0WWSvpIFKw');
            $info = $service->getInformation();
        } catch (\Throwable $e) {
            return $this->fail($e->getMessage());
        }
        return $info;
    }
}
