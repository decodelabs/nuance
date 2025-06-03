<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Nuance\Entity\ConstOption;
use DecodeLabs\Nuance\Entity\NativeObject;
use Redis as RedisObject;

class Redis extends NativeObject
{
    public function __construct(
        RedisObject $redis,
    ) {
        parent::__construct($redis);

        $isConnected = $redis->isConnected();
        $this->meta['connected'] = $isConnected;

        if($isConnected) {
            $this->meta['host'] = $redis->getHost();
            $this->meta['port'] = $redis->getPort();
            $this->meta['auth'] = $redis->getAuth();
            $this->meta['mode'] = new ConstOption($redis->getMode(), [
                '\\Redis::ATOMIC',
                '\\Redis::MULTI',
                '\\Redis::PIPELINE',
            ]);
            $this->meta['dbNum'] = $redis->getDbNum();
            $this->meta['timeout'] = $redis->getTimeout();
            $this->meta['lastError'] = $redis->getLastError();
            $this->meta['persistentId'] = $redis->getPersistentID();
        }
    }
}
