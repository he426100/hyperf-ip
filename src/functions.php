<?php

declare(strict_types=1);
/**
 * This file is part of he426100/hyperf-ip.
 *
 * @link     https://github.com/he426100/hyperf-ip
 * @contact  mrpzx001@gmail.com
 * @license  https://github.com/he426100/hyperf-ip/blob/master/LICENSE
 */
use He426100\IP;
use Hyperf\Utils\ApplicationContext;

if (! function_exists('client_ip')) {
    /**
     * 获取客户端ip.
     */
    function client_ip(): string
    {
        /** @var IP $ip */
        $ip = ApplicationContext::getContainer()->get(IP::class);
        return $ip->getClientIP();
    }
}
