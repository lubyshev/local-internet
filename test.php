<?php

require_once dirname(__FILE__).'/config/bootstrap.php';

use App\Chess\Board;
use App\Chess\Chessmans\Pawn;
use App\Chess\Chessmans\Queen;
use App\Chess\Chessmans\King;

use App\Storage\FileStorage;
use App\Storage\RedisStorage;

function sprintChessPosition( $position ) {
  $result = "==================================\n";
  foreach( $position as $row ) {
    foreach( $row  as $item ) {
      $result .= sprintf('%7s ', $item ? $item : '*');
    }
    $result .= "\n";
  }
  $result .= "==================================\n";
  return $result;
}

echo "\n++++++++++++++++++++++++++++++++++++++++++++++++\n";
echo "\nСоздаем шахматную доску 3x3 ...\n";
$board = new Board( 3 );

echo "Добавляем возможные типы фигур (пешка и ферзь) ...\n";
$board
  -> addChessmanType('pawn', Pawn::class)
  -> addChessmanType('queen', Queen::class);

echo "Добавляем пешку ... "; $caught = false;
try {
  $pawn = new Pawn();
  $board->addChessman( $pawn );
} catch( Exception $exc ) {
  $caught = true;
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: " . $exc->getMessage() ."\n";
} if( ! $caught ) {
  echo " Успешно.\n";
}

echo "Добавляем тот же экземпляр ... "; $caught = false;
try {
  $board->addChessman( $pawn );
} catch( Exception $exc ) {
  $caught = true;
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: " . $exc->getMessage() ."\n";
} if( ! $caught ) {
  echo " Успешно.\n";
}

echo "Добавляем два ферзя ... "; $caught = false;
try {
  $board
    -> addChessman( new Queen())
    -> addChessman( new Queen());
} catch( Exception $exc ) {
  $caught = true;
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: " . $exc->getMessage() ."\n";
} if( ! $caught ) {
  echo " Успешно.\n";
}

echo "Добавляем короля ... "; $caught = false;
try {
  $board
    -> addChessman( new King());
} catch( Exception $exc ) {
  $caught = true;
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: " . $exc->getMessage() ."\n";
} if( ! $caught ) {
  echo " Успешно.\n";
}

echo "Добавляем тип фигуры король ...\n";
$board-> addChessmanType('king', King::class);

echo "Добавляем короля ... "; $caught = false;
try {
  $board
    -> addChessman( new King());
} catch( Exception $exc ) {
  $caught = true;
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: " . $exc->getMessage() ."\n";
} if( ! $caught ) {
  echo " Успешно.\n";
}

echo "Добавляем больше фигур чем клеток на доске ... "; $caught = false;
try {
  $board
    -> addChessman( new Pawn())
    -> addChessman( new Pawn())
    -> addChessman( new Queen())
    -> addChessman( new Pawn())
    -> addChessman( new King())
    -> addChessman( new Pawn())
    -> addChessman( new Queen())
    -> addChessman( new King());
} catch( Exception $exc ) {
  $caught = true;
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: " . $exc->getMessage() ."\n";
} if( ! $caught ) {
  echo " Успешно.\n";
}

echo "\n++++++++++++++++++++++++++++++++++++++++++++++++\n";
echo "\nСоздаем шахматную доску 3x3 ...\n";
$board = new Board( 3 );

echo "Добавляем возможные типы фигур (пешка, ферзь и король) ...\n";
$board
  -> addChessmanType('pawn', Pawn::class)
  -> addChessmanType('queen', Queen::class)
  -> addChessmanType('king', King::class);

echo "Добавляем 3 фигуры на доску ... "; $caught = false;
try {
  $board
    -> addChessman( new Pawn())
    -> addChessman( new Queen())
    -> addChessman( new King());
} catch( Exception $exc ) {
  $caught = true;
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: " . $exc->getMessage() ."\n";
} if( ! $caught ) {
  echo " Успешно.\n";
}

/* @var $chessman \App\Interfaces\IChassman */

echo "\nДобавляем callback как анонимную функцию ... ";
$board->setCallback( function ( $type ) {
  echo "вызван анонимный callback({$type}) ... ";
});

echo "\nПолучаем свободную фигуру ( координаты [0,0] )... ";
if( ! $chessman = $board->getChessmanAt( 0, 0)) {
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: Нет свободных фигур\n";
} else {
  echo " Успешно.\n";
}

echo "Ставим на поле [1,1] ... "; $caught = false;
try {
  $chessman->setAtPosition( 1, 1);
} catch( Exception $exc ) {
  $caught = true;
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: " . $exc->getMessage() ."\n";
} if( ! $caught ) {
  echo " Успешно.\n";
}

echo "Ставим на поле [2,2] ... "; $caught = false;
try {
  $chessman->setAtPosition( 1, 1);
} catch( Exception $exc ) {
  $caught = true;
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: " . $exc->getMessage() ."\n";
} if( ! $caught ) {
  echo " Успешно.\n";
}

echo "Передвигаем на поле [2,2] ... "; $caught = false;
try {
  $chessman->moveToPosition( 2, 2);
} catch( Exception $exc ) {
  $caught = true;
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: " . $exc->getMessage() ."\n";
} if( ! $caught ) {
  echo " Успешно.\n";
}

echo "\nДобавляем callback как метод класса ... ";
class Temp {
  public function callback( $type ) {
    echo "вызван Temp::callback({$type}) ... ";
  }
}
$temp = new Temp();
$board->setCallback( [ $temp, 'callback'] );

echo "\nПолучаем свободную фигуру ... ";
if( ! $chessman = $board->getChessmanAt( 0, 0)) {
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: Нет свободных фигур\n";
} else {
  echo " Успешно.\n";
}

echo "Ставим на поле [2,2] ... "; $caught = false;
try {
  $chessman->setAtPosition( 2, 2);
} catch( Exception $exc ) {
  $caught = true;
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: " . $exc->getMessage() ."\n";
} if( ! $caught ) {
  echo " Успешно.\n";
}

echo "Ставим на поле [1,1] ... "; $caught = false;
try {
  $chessman->setAtPosition( 1, 1);
} catch( Exception $exc ) {
  $caught = true;
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: " . $exc->getMessage() ."\n";
} if( ! $caught ) {
  echo " Успешно.\n";
}

echo "Передвигаем на поле [2,2] ... "; $caught = false;
try {
  $chessman->moveToPosition( 2, 2);
} catch( Exception $exc ) {
  $caught = true;
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: " . $exc->getMessage() ."\n";
} if( ! $caught ) {
  echo " Успешно.\n";
}

echo "Передвигаем на поле [3,3] ... "; $caught = false;
try {
  $chessman->moveToPosition( 3, 3);
} catch( Exception $exc ) {
  $caught = true;
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: " . $exc->getMessage() ."\n";
} if( ! $caught ) {
  echo " Успешно.\n";
}

echo "\nПолучаем свободную фигуру ... ";
if( ! $chessman = $board->getChessmanAt( 0, 0)) {
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: Нет свободных фигур\n";
} else {
  echo " Успешно.\n";
}
echo "Cтавим на поле [1,1] ... ";
try {
  $chessman->setAtPosition( 1, 1);
} catch( Exception $exc ) {
  $caught = true;
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: " . $exc->getMessage() ."\n";
} if( ! $caught ) {
  echo " Успешно.\n";
}

echo "\nПолучаем свободную фигуру ... ";
if( ! $chessman = $board->getChessmanAt( 0, 0)) {
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: Нет свободных фигур\n";
} else {
  echo " Успешно.\n";
}

echo "\n++++++++++++++++++++++++++++++++++++++++++++++++\n";
echo "\nСоздаем шахматную доску 4x4 и расставляем фигуры ...\n";
$board = new Board( 4 );
$board
  -> addChessmanType('pawn', Pawn::class)
  -> addChessmanType('queen', Queen::class)
  -> addChessmanType('king', King::class)
  -> addChessman( new Pawn())
  -> addChessman( new Pawn())
  -> addChessman( new Queen())
  -> addChessman( new Queen())
  -> addChessman( new King());
while( $chessman = $board->getChessmanAt( 0, 0) ) {
  do {
    $row = rand(1, 4);
    $column = rand(1, 4);
  } while( ! $board->cellIsEmpty( $row, $column) );
  $chessman->setAtPosition( $row, $column);
}
echo sprintChessPosition( $board->getChessPosition());

echo "\nЗаписываем позицию ... "; $caught = false;
try {
  $board->stateSave( 'position_1' );
} catch( Exception $exc ) {
  $caught = true;
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: " . $exc->getMessage() ."\n";
} if( ! $caught ) {
  echo " Успешно.\n";
}

$fileStorage = new FileStorage();
echo "\nЗаписываем позицию с указанием хранилища (файл)... "; $caught = false;
try {
  $board->stateSave( 'position_1', $fileStorage);
} catch( Exception $exc ) {
  $caught = true;
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: " . $exc->getMessage() ."\n";
} if( ! $caught ) {
  echo " Успешно.\n";
}

echo "\nСоздаем шахматную доску 1x1 c файловым хранилищем по умолчанию ...\n";
$board = new Board( 1, $fileStorage );
echo "\nЗагружаем позицию ... "; $caught = false;
try {
  $board->stateLoad( 'position_1');
} catch( Exception $exc ) {
  $caught = true;
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: " . $exc->getMessage() ."\n";
} if( ! $caught ) {
  echo " Успешно.\n";
  echo sprintChessPosition( $board->getChessPosition());
}

$redisStorage = new RedisStorage();
echo "\nЗаписываем позицию с указанием хранилища (redis)... "; $caught = false;
try {
  $board->stateSave( 'position_1', $redisStorage);
} catch( Exception $exc ) {
  $caught = true;
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: " . $exc->getMessage() ."\n";
} if( ! $caught ) {
  echo " Успешно.\n";
}

echo "\nСоздаем шахматную доску 1x1 ...\n";
$board = new Board( 1 );
echo "\nЗагружаем позицию ... "; $caught = false;
try {
  $board->stateLoad( 'position_1');
} catch( Exception $exc ) {
  $caught = true;
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: " . $exc->getMessage() ."\n";
} if( ! $caught ) {
  echo " Успешно.\n";
  echo sprintChessPosition( $board->getChessPosition());
}

echo "\nЗагружаем позицию с указанием хранилища (redis) ... "; $caught = false;
try {
  $board->stateLoad( 'position_1', $redisStorage);
} catch( Exception $exc ) {
  $caught = true;
  echo " НЕУДАЧА!\n";
  echo "    Ошибка: " . $exc->getMessage() ."\n";
} if( ! $caught ) {
  echo " Успешно.\n";
  echo sprintChessPosition( $board->getChessPosition());
}
