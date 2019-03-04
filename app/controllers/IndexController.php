<?php

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        if (empty($_SESSION['user_id'])) {
            return $this->response->redirect('login/index', true);
        }

        $this->view->user_id = $_SESSION['user_id'];
        $this->view->user_name = $_SESSION['user_name'];
    }

}

