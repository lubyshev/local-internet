<?php

namespace App\Interfaces;

interface IChassman {

  /**
   * Is the chessman at position
   *
   * @param int $row
   * @param int $column
   *
   * @return bool
   */
  public function atPosition( $row, $column );

  /**
   * Returns position of the chessman
   *
   * @return array [row,column] coordinates of the chessman
   */
  public function getPosition();

  /**
   * Returns type name of a chessman
   *
   * @return string Unique type name
   */
  public function getType();

  /**
   * Move the chassman at the board position
   *
   * @param int $row
   * @param int $column
   *
   * @return \App\Interfaces\IChassman
   */
  public function moveToPosition( $row, $column );

  /**
   * Set the chassman at the board position
   *
   * @param int $row
   * @param int $column
   *
   * @return \App\Interfaces\IChassman
   */
  public function setAtPosition( $row, $column );

  /**
   * Assigns a parent chess board
   *
   * @param \App\Chess\Board $board Chess board
   *
   * @return \App\Interfaces\IChassman
   */
  public function setToBoard( \App\Chess\Board $board );

}