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

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'listeners' => [],
            'aspects' => [],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The configuration file of IP.',
                    'source' => __DIR__ . '/../publish/ip.php',
                    'destination' => BASE_PATH . '/config/autoload/ip.php',
                ],
            ],
        ];
    }
}
