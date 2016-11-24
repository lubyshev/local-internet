<?php

namespace App\Storage;

use App\Chess\Board;
use App\Interfaces\IStorage;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CacheStorageAbstractException extends \Exception {}

abstract class CacheStorageAbstract implements IStorage {

  protected $storage = null;

  /**
   * Constructor
   *
   */
  public function __construct() {
    $this->setStorage();
  }

  protected function checkStorage() {
    if( ! $this->storage ) {
      throw new CacheStorageAbstractException('Storage does not assigned');
    }
    if( ! $this->storage instanceof CacheItemPoolInterface ) {
      throw new CacheStorageAbstractException('Storage must be instance of Psr\\Cache\\CacheItemPoolInterface');
    }
  }

  /**
   * Check if a board state exists
   *
   * @param string $name Unique name of a chess board
   *
   * @return bool TRUE on success, FALSE otherwise
   */
  public function exists( $name ) {
    $this->checkStorage();
    return $this->storage->hasItem( $name );
  }

  /**
   * Load chess board state
   *
   * @param string $name Unique name of a chess board
   * @param \App\Chess\Board $board Board instance
   *
   * @return bool TRUE on success, FALSE otherwise
   */
  public function load( $name, Board $board ) {
    $this->checkStorage();
    $result = false;
    if( $this->exists( $name )) {
      $cahceItem = $this->storage->getItem( $name );
      $board->stateDecode( $cahceItem->get());
      $result = true;
    }
    return $result;
  }

  /**
   * Save chess board state
   *
   * @param string $name Unique name of a chess board
   * @param \App\Chess\Board $board Board instance
   *
   * @return bool TRUE on success, FALSE otherwise
   */
  public function save( $name, Board $board) {
    $this->checkStorage();
    $cahceItem = $this->storage->getItem( $name );
    $cahceItem->set( $board->stateEncode());
    return $this->storage->save( $cahceItem );
  }

  /**
   * Assigns the Psr\Cache\CacheItemPoolInterface instance
   * to the $storage property
   */
  abstract protected function setStorage();

}
