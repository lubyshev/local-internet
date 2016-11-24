<?php

namespace App\Chess\Chessmans;

use App\Chess\Chessmans\ChessmanAbstract;

class Pawn extends ChessmanAbstract {

  /**
   * Returns type name of a chessman
   *
   * @return string Unique type name
   */
  public function getType() {
    return 'pawn';
  }

}
