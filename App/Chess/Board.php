<?php

namespace App\Chess;

use App\Interfaces\IStorage;
use App\Interfaces\IChassman;

class BoardException extends \Exception {}

class Board {

  protected $chessmans;

  protected $chessmanTypes;

  protected $dimension;

  protected $onSetCallback;

  protected $storage;

  /**
   * Constructor
   *
   * @param int $dimension Dimension of the board
   * @param \App\Interfaces\IStorage $storage Storage instance
   */
  public function __construct( $dimension, IStorage $storage = null) {
    $this->dimension = (int) $dimension;
    if( $storage ) {
      $this->storage = $storage;
    } else {
      $this->storage = null;
    }
  }

  /**
   * Add a custom chessman to the board
   *
   * @param IChassman $chessman
   * @return \App\Chess\Board
   * @throws BoardException
   */
  public function addChessman( IChassman $chessman ) {
    $chessmanType = $chessman->getType();
    if( ! isset( $this->chessmanTypes[ $chessmanType ])) {
      throw new BoardException("Invalid chessman type ({$chessmanType})");
    }
    if( count( $this->chessmans ) > $this->dimension * $this->dimension ) {
      throw new BoardException("The board are full");
    }
    $chessman->setToBoard( $this );
    $this->chessmans [] = $chessman;
    return $this;
  }

  /**
   * Add custom chessman type
   *
   * @param type $chessmanType Unique type name
   * @param type $chessmanClass Class of the chessman
   *
   * @return \App\Chess\Board
   * @throws BoardException
   */
  public function addChessmanType( $chessmanType, $chessmanClass ) {
    if( ! isset( $this->chessmanTypes[ $chessmanType ])) {
      $this->chessmanTypes[ $chessmanType ] = $chessmanClass;
    } else {
      throw new BoardException("Chessmen type {$chessmanType} already assigned");
    }
    return $this;
  }

  /**
   * Check if position on the board is empty
   *
   * @param type $row Row
   * @param type $column Column
   *
   * @return boolean
   */
  public function cellIsEmpty( $row, $column ) {
    $result = true;
    foreach( $this->chessmans as $item ) {
      /* @var $item \App\Interfaces\IChassman */
      if( $item->atPosition( $row, $column)) {
        $result = false;
        break;
      }
    }
    return $result;
  }

  /**
   * Check positon coordinates
   *
   * @param type $row
   * @param type $column
   * @throws BoardException
   */
  protected function checkPosition( $row, $column ) {
    if(  $row < 0
      || $column < 0
      || $row > $this->dimension
      || $column > $this->dimension
      || ( ! $row && $column)
      || ( ! $column && $row)
    ) {
      throw new BoardException("Invalid chessman position ({$row},{$column})");
    }
  }

  /**
   * Return chessman at specific positioin
   *
   * @param type $row Row
   * @param type $column Column
   *
   * @return \App\Interfaces\IChassman
   */
  public function getChessmanAt( $row, $column ) {
    $this->checkPosition( $row, $column);
    $result = null;
    foreach( $this->chessmans as $item ) {
      /* @var $item \App\Interfaces\IChassman */
      if( $item->atPosition( $row, $column)) {
        $result = $item;
        break;
      }
    }
    return $result;
  }

  /**
   * Returns the board dimension
   *
   * @return int
   */
  public function getDimension() {
    return $this->dimension;
  }

  /**
   * Returns current chess position as array
   *
   * @return array
   */
  public function getChessPosition() {
    $position = [];
    $row = 0;
    while( $row++ < $this->dimension) {
      $position [$row] = array_fill( 1, $this->dimension, null);
    }
    foreach( $this->chessmans as $item ) {
      /* @var $item \App\Interfaces\IChassman */
      $itemPos = $item->getPosition();
      if( [0, 0] === $itemPos ) {
        $position [0] [] = $item->getType();
      } else {
        $position[ $itemPos[ 0 ]] [ $itemPos[ 1 ]] = $item->getType();
      }
    }
    return $position;
  }

  /**
   * Fired when a chessmen sets on the board
   * @param string $type Chessman type
   */
  public function onSet( $type ) {
    if( isset( $this->onSetCallback)) {
      if( is_callable( $this->onSetCallback)) {
        $func = $this->onSetCallback;
        $func( $type );
      }
    }
  }

  /**
   * Reset the board
   */
  public function reset() {
    $this->dimension = 0;
    $this->chessmanTypes = [];
    $this->chessmans = [];
  }

  /**
   * Sets callback triggered when a chessmen sets on the board
   *
   * @param mixed $callback Closure or array [class,method]
   */
  public function setCallback( $callback ) {
    $this->onSetCallback = $callback;
  }

  /**
   * Encode current board state
   *
   * @return string
   */
  public function stateEncode() {
    $data = [ 'dimension' => $this->dimension ];
    if( ! empty( $this->chessmanTypes)) {
      $data[ 'types' ] = $this->chessmanTypes;
      if( ! empty( $this->chessmans )) {
        $data[ 'chessmans' ] = [];
        foreach( $this->chessmans as $item ) {
          /* @var $item \App\Interfaces\IChassman */
          $chessman = [ 'type' => $item->getType() ];
          $position = $item->getPosition();
          if( [ 0, 0] !== $position ) {
            $chessman[ 'position' ] = $position;
          }
          $data ['chessmans'] [] = $chessman;
        }
      }
    }
    return json_encode( $data );
  }

  /**
   * Restore board state from previously encoded board state
   *
   * @param string $state Encoded board state
   *
   * @return bool TRUE on success, FALSE otherwise
   * @throws BoardException
   */
  public function stateDecode( $state ) {
    if( ! $data = json_decode( $state, true)) {
      throw new BoardException("Invalid state data");
    }
    if( ! isset( $data[ 'dimension' ])) {
      throw new BoardException("Dimension is not defined");
    }
    $this->reset();

    $this->dimension = $data[ 'dimension' ];

    if( isset( $data[ 'types' ]) && is_array( $data[ 'types' ])) {
      $this->chessmanTypes = $data ['types'];
      if( isset( $data ['chessmans'])) {
        foreach( $data ['chessmans'] as $chessman ) {
          $chessmanClass = $this->chessmanTypes[ $chessman[ 'type' ]];
          $newOne = new $chessmanClass();
          /* @var $newOne \App\Interfaces\IChassman */
          $this->addChessman( $newOne );
          if( isset( $chessman[ 'position' ])) {
            $newOne->setAtPosition( $chessman[ 'position' ] [0], $chessman[ 'position' ] [1]);
          }
        }
      }
    }
    return true;
  }

  /**
   * Load state of a board from storage
   *
   * @param string $name Saved state name
   * @param \App\Interfaces\IStorage $storage Storage instance
   * @return bool TRUE on success, FALSE otherwise
   * @throws BoardException
   */
  public function stateLoad( $name, IStorage $storage = null) {
    if( ! $storage ) {
      $storage = $this->storage;
    }
    if( ! $storage ) {
      throw new BoardException("Storage does not assigned");
    }
    return $storage->load( $name, $this );
  }

  /**
   * Save the current board state to a storage
   * @param string $name Name of saved state
   * @param \App\Interfaces\IStorage $storage Storage instance
   * @return bool TRUE on success, FALSE otherwise
   * @throws BoardException
   */
  public function stateSave( $name, IStorage $storage = null) {
    if( ! $storage ) {
      $storage = $this->storage;
    }
    if( ! $storage ) {
      throw new BoardException("Storage does not assigned");
    }
    return $storage->save( $name, $this );
  }

}