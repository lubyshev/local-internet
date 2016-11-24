# Тестовое задание

## Требования

Должны быть установлены:

1. [Git] (https://git-scm.com/)
2. [Composer] (https://getcomposer.org/)
3. [Redis] (http://redis.io/)

## Задача
[https://docs.google.com/document/d/1YsE19WnJjftWjNycPYfDCw8OtbObWekhRV0DaW0y0Xc/edit] (https://docs.google.com/document/d/1YsE19WnJjftWjNycPYfDCw8OtbObWekhRV0DaW0y0Xc/edit)

## Решение

### Хранилища

1. Так как в задаче выставлено требование возможности внедрения произвольных хранилищ,
доступ к хранилищам реализован через интерфейс *App\Interfaces\IStorage*.
2. Требовалось реализовать хранение в файловой системе и redis. Эти способы хранения
реализованы в большинстве современных кешей. Поэтому в качестве основы был выбран *symfony/cache*.
3. Чтобы избежать дублирования кода был создан абстрактный базовый класс *App\Storage\CacheStorageAbstract*.
4. Для возможности использования других кешей был задействован *psr/cache-implementation*.

### Фигуры

1. Так как в задаче выставлено требование возможности внедрения любого количества
фигур, все манипуляции с ними проводятся через интерфейс *App\Interfaces\IChassman*.
2. Чтобы избежать дублирования кода был создан абстрактный базовый класс *App\Chess\Chessmans\ChessmanAbstract*.

### Доска

1. Возможность вызова пользовательского кода в момент добавления фигуры реализована с помощью колбека
*App\Chess\Board::setCallback()*.
2. Колбеки можно задавать как с помощью анонимных функций, так и через методы классов.

## Установка

Зайдите в произвольную папку *[folder]*. Проект мы разместим в папке *[subfolder]*

```bash

    git clone https://github.com/lubyshev/local-internet.git [subfolder]
    cd [subfolder]
    composer install

```

## Запуск

Просто запустите файл *test.php*

```bash
    
    php test.php

```
