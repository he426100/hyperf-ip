<?php

declare(strict_types=1);
/**
 * This file is part of he426100/hyperf-ip.
 *
 * @link     https://github.com/he426100/hyperf-ip
 * @contact  mrpzx001@gmail.com
 * @license  https://github.com/he426100/hyperf-ip/blob/master/LICENSE
 */

namespace He426100;

use Hyperf\Context\Context;
use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Contract\RequestInterface;

/**
 * 获取客户端ip.
 * @see https://github.com/top-think/framework/blob/HEAD/src/think/Request.php
 * @see https://github.com/symfony/symfony/blob/efb5c4907f54ddc84558736df692e7b1033fd2a3/src/Symfony/Component/HttpFoundation/Request.php#L775-L822
 * @see https://github.com/symfony/symfony/blob/808977847fa40f8323c4c850f8ae6f58cffad754/src/Symfony/Component/HttpFoundation/IpUtils.php#L37
 */
class IP
{
    public function __construct(private ConfigInterface $config, private RequestInterface $request)
    {
    }

    /**
     * 获取客户端ip.
     */
    public function getClientIP(): string
    {
        $realIP = '';
        $key = IP::class;
        if (Context::has($key)) {
            $realIP = Context::get($key);
        }
        if (!empty($realIP)) {
            return $realIP;
        }

        $realIP = $this->request->server('remote_addr', '');

        // 如果获取到的客户端Ip属于可信代理ip则从header中获取真实ip
        $config = $this->config->get('ip', []);
        $trustedProxies = $config['proxy'] ?? [];
        if (count($trustedProxies) > 0) {
            // 先这么写着，暂时只碰到过x-forwarded-for
            $tempIP = $this->request->header('x-forwarded-for');
            $tempIP = trim(explode(',', $tempIP)[0]);
            if (!$this->isValidIP($tempIP)) {
                $tempIP = null;
            }
            // tempIP不为空，说明获取到了一个IP地址
            // 这时我们检查 REMOTE_ADDR 是不是指定的前端代理服务器之一
            // 如果是的话说明该 IP头 是由前端代理服务器设置的
            // 否则则是伪装的
            if (!empty($tempIP)) {
                $realIPBin = $this->ip2bin($realIP);
                foreach ($trustedProxies as $ip) {
                    $serverIPElements = explode('/', $ip);
                    $serverIP = $serverIPElements[0];
                    $serverIPPrefix = $serverIPElements[1] ?? 128;
                    $serverIPBin = $this->ip2bin($serverIP);

                    // IP类型不符
                    if (strlen($realIPBin) !== strlen($serverIPBin)) {
                        continue;
                    }

                    if (strncmp($realIPBin, $serverIPBin, (int) $serverIPPrefix) === 0) {
                        $realIP = $tempIP;
                        break;
                    }
                }
            }
        }

        if (!$this->isValidIP($realIP)) {
            $realIP = '0.0.0.0';
        }

        return Context::set($key, $realIP);
    }

    /**
     * 检测是否是合法的IP地址
     *
     * @param string $ip IP地址
     * @param string $type IP地址类型 (ipv4, ipv6)
     */
    public function isValidIP(string $ip, string $type = ''): bool
    {
        switch (strtolower($type)) {
            case 'ipv4':
                $flag = FILTER_FLAG_IPV4;
                break;
            case 'ipv6':
                $flag = FILTER_FLAG_IPV6;
                break;
            default:
                $flag = 0;
                break;
        }

        return boolval(filter_var($ip, FILTER_VALIDATE_IP, $flag));
    }

    /**
     * 将IP地址转换为二进制字符串.
     */
    public function ip2bin(string $ip): string
    {
        if ($this->isValidIP($ip, 'ipv6')) {
            $IPHex = str_split(bin2hex(inet_pton($ip)), 4);
            foreach ($IPHex as $key => $value) {
                $IPHex[$key] = intval($value, 16);
            }
            $IPBin = vsprintf('%016b%016b%016b%016b%016b%016b%016b%016b', $IPHex);
        } else {
            $IPHex = str_split(bin2hex(inet_pton($ip)), 2);
            foreach ($IPHex as $key => $value) {
                $IPHex[$key] = intval($value, 16);
            }
            $IPBin = vsprintf('%08b%08b%08b%08b', $IPHex);
        }

        return $IPBin;
    }
}
