<?php

declare(strict_types=1);
/**
 * This file is part of he426100/hyperf-client-ip.
 *
 * @link     https://github.com/he426100/hyperf-client-ip
 * @contact  mrpzx001@gmail.com
 * @license  https://github.com/he426100/hyperf-client-ip/blob/master/LICENSE
 */
use He426100\ClientIP\ClientIP;
use Hyperf\Context\ApplicationContext;

if (! function_exists('client_ip')) {
    /**
     * 获取客户端ip.
     */
    function client_ip(): string
    {
        /** @var ClientIP $ip */
        $ip = ApplicationContext::getContainer()->get(ClientIP::class);
        return $ip->getClientIP();
    }
}
