<?php

use Phalcon\Mvc\Controller;

/**
 * Class ControllerBase
 *
 * @property \Phalcon\Cache\Backend\File $dataCache
 * @property \Phalcon\Config $config
 */

class ControllerBase extends Controller
{
    public function successResponse($data)
    {
        $result = [];
        $result['success'] = true;
        $result['code'] = 200;
        $result['message'] = 'successful';

        $result['data'] = $data;

        return $this->response->setJsonContent($result);
    }
}
