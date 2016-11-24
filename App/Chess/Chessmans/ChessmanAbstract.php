<?php

namespace App\Chess\Chessmans;

use App\Chess\Board;
use App\Interfaces\IChassman;

class ChessmanException extends \Exception {}

abstract class ChessmanAbstract implements IChassman {

  protected $board = null;

  protected $column = 0;

  protected $onBoard = false;

  protected $row = 0;

  /**
   * Is the chessman at position
   *
   * @param int $row
   * @param int $column
   *
   * @returns bool
   */
  public function atPosition( $row, $column ) {
    $result = false;
    if( $row === $this->row && $column === $this->column) {
      $result = true;
    }
    return $result;
  }

  /**
   * Check ability to move the chessman
   *
   * @param int $row
   * @param int $column
   *
   */
  protected function checkMovementAbility( $row, $column ) {
    if( ! $this->board ) {
      throw new ChessmanException("Board does not assigned");
    }
    $dimension = $this->board->getDimension();
    if(  $row <= 0
      || $column <= 0
      || $row > $dimension
      || $column > $dimension
    ) {
      throw new ChessmanException("Invalid chessman position ({$row},{$column})");
    }
    if( ! $this->board->cellIsEmpty( $row, $column)) {
      throw new ChessmanException("Cell ({$row},{$column}) are not empty");
    }
  }

  /**
   * Returns position of the chessman
   *
   * @return array [row,column] coordinates of the chessman
   */
  public function getPosition() {
    return [ $this->row, $this->column ];
  }

  /**
   * Returns type name of a chessman
   *
   * @return string Unique type name
   */
  abstract public function getType();

  /**
   * Move the chassman at the board position
   *
   * @param int $row
   * @param int $column
   *
   * @return \App\Interfaces\IChassman
   */
  public function moveToPosition( $row, $column ) {
    if( $this->onBoard ) {
      $this->checkMovementAbility( $row, $column);
      $this->row = $row;
      $this->column = $column;
    } else {
      throw new ChessmanException('Not at the board');
    }
    return $this;
  }

  /**
   * Set the chassman at the board position
   *
   * @param int $row
   * @param int $column
   *
   * @return \App\Interfaces\IChassman
   */
  public function setAtPosition( $row, $column ) {
    if( ! $this->onBoard ) {
      $this->checkMovementAbility( $row, $column);
      $this->row = $row;
      $this->column = $column;
      $this->onBoard = true;
      $this->board->onSet( $this->getType());
    } else {
      throw new ChessmanException('Already on board');
    }
    return $this;
  }

  /**
   * Assigns a parent chess board
   *
   * @param \App\Chess\Board $board Chess board
   *
   * @return \App\Interfaces\IChassman
   */
  public function setToBoard( \App\Chess\Board $board ) {
    if( ! $this->board ) {
      $this->board = $board;
      $this->row = 0;
      $this->column = 0;
    } else {
      throw new ChessmanException('Board already assigned');
    }
    return $this;
  }

}
