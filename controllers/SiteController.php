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
        $url = Core::$app->get('url');

        if (empty($url)) {
            return $this->fail('The url or html-code not found');
        }
        try {
            $service = VideoServiceFactory::create($url);
            if (is_null($service)) {
                return $this->fail('Our service is not working with this video service');
            }
            $info = $service->getInformation();
        } catch (\Throwable $e) {
            return $this->fail($e->getMessage());
        }
        return $info;
    }
}
