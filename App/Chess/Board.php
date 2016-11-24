<?php

namespace App\Chess;

use App\Interfaces\IStorage;
use App\Interfaces\IChassman;

class BoardException extends \Exception {}

class Board {

  protected $chessmans;

  protected $chessmanTypes;

  protected $dimension;

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

  public function addChessmanType( $chessmanType, $chessmanClass ) {
    if( ! isset( $this->chessmanTypes[ $chessmanType ])) {
      $this->chessmanTypes[ $chessmanType ] = $chessmanClass;
    } else {
      throw new BoardException("Chessmen type {$chessmanType} already assigned");
    }
    return $this;
  }

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

  public function getDimension() {
    return $this->dimension;
  }

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

  public function reset() {
    $this->dimension = 0;
    $this->chessmanTypes = [];
    $this->chessmans = [];
  }

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

  public function stateLoad( $name, IStorage $storage = null) {
    if( ! $storage ) {
      $storage = $this->storage;
    }
    if( ! $storage ) {
      throw new BoardException("Storage does not assigned");
    }
    return $storage->load( $name, $this );
  }

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