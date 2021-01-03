## ArrayObjectMap

![https://img.shields.io/badge/PHP-7.4.0-blue](https://img.shields.io/badge/PHP-7.4.0-blue)

- Оболочка для ассоциативных массивов
  - Wrapper for associative arrays

  - [Эффективные структуры данных для `PHP 7`, представленные как альтернативы для типа `array`](https://www.php.net/manual/ru/book.ds.php)

____

## Requirements

- PHP `7.2` или выше
  - PHP `7.2` or higher
  - [Расширение PHP Pecl DS](https://pecl.php.net/package/ds)
  - [Класс Ds\Map](https://www.php.net/manual/ru/class.ds-map.php)

## Installation

- Скачайте эту библиотеку и подключите файл в вашем скрипте.
  - Download this library and require the necessary file directly in your script.


``` php
require __DIR__ . '/path/to/library/ArrayObjectMap.php';
```

## Creation

- Создайте новый класс наследуемый от класса ArrayObjectMap

``` php
class User extends ArrayObjectMap {

    public function set($key, $value)
    {
        $this->_changed[] = $key;
        return parent::set($key, $value);
    }

    public function load()
    {
        return $this;
    }
}
```

## Usage

``` php
require __DIR__ . '/path/to/library/ArrayObjectMap.php';

class User extends ArrayObjectMap {

    public function set($key, $value)
    {
        $this->_changed[] = $key;
        return parent::set($key, $value);
    }

    public function load()
    {
        return $this;
    }
}

class Page extends ArrayObjectMap {

    public function set($key, $value)
    {
        $this->_changed[] = $key;
        return parent::set($key, $value);
    }

    public function save()
    {
        return $this;
    }
}

$user = new User;

$user->load()
     ->setDima(['id' => 1, 'email' => 'dima@example.com'])
     ->setVasya(['id' => 2, 'email' => 'vasya@example.com'])
     ->setpage(
         (new Page)
             ->setTitle('List users')
             ->setDescription('All users site')
             ->setUsers(clone $user)
             ->save()
     );
```

## Доступ к данным

- Пример:

``` php
foreach ($user->page->users as $name => $value)
{
    echo nl2br(ucfirst($name) .': '.$value->email .PHP_EOL, FALSE);
}
```

``` php
var__dump($user);
```
