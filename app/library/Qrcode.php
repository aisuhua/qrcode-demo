<?php
class Qrcode
{
    const STATE_INIT = 0; // 初始化
    const STATE_SCANNED = 1; // 已扫描
    const STATE_CONFIRMED = 2; // 已使用
    const STATE_EXPIRED = -1; // 已过期
    const STATE_CANCELED = -2; // 已取消

    const UUID_TTL = 600;
}