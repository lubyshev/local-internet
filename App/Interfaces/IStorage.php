<?php

namespace App\Interfaces;

use App\Chess\Board;

interface IStorage {

  /**
   * Check if a board exists
   *
   * @param string $name Unique name of a chess board
   *
   * @return bool TRUE on success, FALSE otherwise
   */
  public function exists( $name );

  /**
   * Load chess board state
   *
   * @param string $name Unique name of a chess board
   * @param \App\Chess\Board $board Board instance
   *
   * @return bool TRUE on success, FALSE otherwise
   */
  public function load( $name, Board $board );


  /**
   * Save chess board state
   *
   * @param string $name Unique name of a chess board
   * @param \App\Chess\Board $board Board instance
   *
   * @return bool TRUE on success, FALSE otherwise
   */
  public function save( $name, Board $board);

}