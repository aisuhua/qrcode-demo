<?php

class LoginController extends ControllerBase
{
    /**
     * 登录页
     */
    public function indexAction()
    {
        if (!empty($_SESSION['user_id'])) {
            return $this->response->redirect('index', true);
        }
    }

    /**
     * 使用 qrcode 进行登录（适用于 Web 端）
     * 网页端获知该二维码已登录的情况下，使用 uuid 调用该接口完成登录操作
     */
    public function qrcodeAction()
    {
        $ticket = $this->request->get('ticket', 'string', '');
        $uuid = $this->request->get('uuid', 'string', '');
        $time = $this->request->get('time', 'int', '');

        $key = '123456';
        if(sha1($uuid . $key . $time) !== $ticket) {
            return $this->failedResponse(-1, '签名错误');
        }

        // 手机端确认登录后，网页跳转时间不能超过10分钟
        if (time() - $time > 600) {
            return $this->failedResponse(-2, '登录超时');
        }

        // 验证二维码状态是否为已确定
        $uuid_info = $this->dataCache->get($uuid);
        if (empty($uuid_info) || $uuid_info['state'] != Qrcode::STATE_CONFIRMED) {
            return $this->failedResponse(-3, '二维码已过期');
        }

        // 设置为已登录状态
        $_SESSION['user_id'] = 10001;
        $_SESSION['user_name'] = '小张';

        // 删除使用过的二维码
        $this->dataCache->delete($uuid);

        return $this->successResponse();
    }
}

