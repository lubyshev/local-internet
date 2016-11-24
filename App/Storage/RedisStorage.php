<?php

namespace App\Storage;

use App\Storage\CacheStorageAbstract;
use Predis\Client as RedisClient;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class RedisStorage extends CacheStorageAbstract {

  protected function setStorage() {
    $this->storage = new RedisAdapter( new RedisClient());
  }

}
