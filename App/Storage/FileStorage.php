<?php

namespace App\Storage;

use App\Storage\CacheStorageAbstract;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class FileStorage extends CacheStorageAbstract {

  protected function setStorage() {
    $this->storage = new FilesystemAdapter();
  }

}