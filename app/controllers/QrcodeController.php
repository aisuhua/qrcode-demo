<?php

class QrcodeController extends ControllerBase
{
    /**
     * 获取一个 uuid（适用于 Web 端，未登录状态）
     */
    public function getAction()
    {
        $uuid = uniqid('', true);
        $data = [
            'state' => Qrcode::STATE_INIT
        ];

        $this->dataCache->save($uuid, $data, Qrcode::UUID_TTL);

        $config = $this->di->getShared('config');
        $result = [
            'uuid' => $uuid,
            'qrcode' => $config->site->host . '/qrcode/scan?uuid=' . $uuid
        ];

        return $this->successResponse($result);
    }

    /**
     * 检查二维码的状态（适用于 Web 端，未登录状态）
     */
    public function checkAction()
    {
        $uuid = $this->request->get('uuid', 'string', '');

        $result = [];
        $data = $this->dataCache->get($uuid);

        if (empty($data)) {
            $result['state'] = Qrcode::STATE_EXPIRED;
            $result['tip'] = '二维码已过期';
        } elseif ($data['state'] == Qrcode::STATE_INIT) {
            $result['state'] = Qrcode::STATE_INIT;
            $result['tip'] = '二维码有效，等待用户使用手机扫描';
        } elseif ($data['state'] == Qrcode::STATE_CANCELED) {
            $result['state'] = Qrcode::STATE_CANCELED;
            $result['tip'] = '二维码已被用户取消';
        } elseif ($data['state'] == Qrcode::STATE_SCANNED) {
            $result['state'] = Qrcode::STATE_SCANNED;
            $result['tip'] = '二维码已被扫描，等待用户在手机点击确认';
            $result['user_face'] = '/img/face.png';
        } elseif ($data['state'] == Qrcode::STATE_CONFIRMED) {
            $result['state'] = Qrcode::STATE_CONFIRMED;
            $result['tip'] = '已经登录';

            // 生成登录密钥
            $config = $this->di->getShared('config');
            $key = '123456';
            $time = time();
            $ticket = sha1($uuid . $key . $time);
            $result['login_url'] = $config->site->host . '/login/qrcode?ticket='. $ticket .'&uuid=' . $uuid . '&time=' . $time;
        }

        return $this->successResponse($result);
    }

    /**
     * 扫描该二维码（适用于移动端，用户处于已登录状态）
     * http://qrcode.demo.com/qrcode/scan?uuid=5c7c9d94d7aeb4.76940700
     */
    public function scanAction()
    {
        $uuid = $this->request->get('uuid', 'string', '');

        // 模拟获取当前的用户信息（一般使用移动端的 Cookie 来验证用户身份）
        $user_id = 10001;

        $uuid_info = $this->dataCache->get($uuid);

        if (!$uuid_info || $uuid_info['state'] != Qrcode::STATE_INIT) {
            return $this->failedResponse(
                Qrcode::STATE_EXPIRED,
                '二维码已过期'
            );
        }

        // 将 uuid 状态修改为已扫描
        $data = [
            'state' => Qrcode::STATE_SCANNED
        ];
        $this->dataCache->save($uuid, $data, Qrcode::UUID_TTL);

        $config = $this->di->getShared('config');
        $result = [];
        $result['confirm_url'] = $config->site->host . '/qrcode/confirm?uuid=' . $uuid;
        $result['cancel_url'] = $config->site->host . '/qrcode/cancel?uuid=' . $uuid;

        return $this->successResponse($result);
    }

    /**
     * 确认登录（适用于移动端，用户处于已登录状态）
     */
    public function confirmAction()
    {
        $uuid = $this->request->get('uuid', 'string', '');

        // 模拟获取当前的用户信息（一般使用移动端的 Cookie 来验证用户身份）
        $user_id = 10001;

        $uuid_info = $this->dataCache->get($uuid);

        if (!$uuid_info || $uuid_info['state'] != Qrcode::STATE_SCANNED) {
            return $this->failedResponse(
                Qrcode::STATE_EXPIRED,
                '二维码已过期'
            );
        }

        // 将 uuid 状态修改为已确认登录
        $data = [
            'state' => Qrcode::STATE_CONFIRMED
        ];
        $this->dataCache->save($uuid, $data, Qrcode::UUID_TTL);

        return $this->successResponse();
    }

    /**
     * 取消登录（适用于移动端，用户处于已登录状态）
     */
    public function cancelAction()
    {
        $uuid = $this->request->get('uuid', 'string', '');

        // 模拟获取当前的用户信息（一般使用移动端的 Cookie 来验证用户身份）
        $user_id = 10001;

        $uuid_info = $this->dataCache->get($uuid);
        if ($uuid_info) {
            $data = [
                'state' => Qrcode::STATE_CANCELED
            ];
            $this->dataCache->save($uuid, $data, Qrcode::UUID_TTL);
        }

        return $this->successResponse();
    }
}

