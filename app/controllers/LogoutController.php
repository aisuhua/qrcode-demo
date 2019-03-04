<?php

class LogoutController extends ControllerBase
{
    public function exitAction() {
        session_unset();
        session_destroy();

        return $this->response->redirect('/login', true);
    }
}

