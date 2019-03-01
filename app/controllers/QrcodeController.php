<?php

class QrcodeController extends ControllerBase
{
    /**
     * 获取一个 uuid（适用于 Web 端）
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
            'qrcode' => $config->site->host . '/scan?uuid=' . $uuid
        ];

        return $this->successResponse($result);
    }

    /**
     * 检查二维码的状态（适用于 Web 端）
     */
    public function checkAction()
    {
        $uuid = $this->request->get('uuid', 'string', '');

        $result = [];
        $data = $this->dataCache->get($uuid);

        $data = [];

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
        } elseif ($data['state'] == Qrcode::STATE_CONFIRMED) {
            $result['state'] = Qrcode::STATE_CONFIRMED;
            $result['tip'] = '登录成功';
        }

        return $this->successResponse($result);
    }

    /**
     * 扫描该二维码（适用于移动端）
     */
    public function scanAction()
    {

    }

    /**
     * 确认登录（适用于移动端）
     */
    public function confirmAction()
    {

    }
}

