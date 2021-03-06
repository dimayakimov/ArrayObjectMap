## ArrayObjectMap

[![https://img.shields.io/badge/PHP-7.4.0-blue](https://img.shields.io/badge/PHP-7.4.0-blue)](https://www.php.net/releases/7_4_0.php)
[![https://img.shields.io/badge/PHP_Pecl_DS-1.3.0-blue](https://img.shields.io/badge/PHP_Pecl_DS-1.3.0-blue)](https://pecl.php.net/package/ds)


- Оболочка для ассоциативных массивов
  - Wrapper for associative arrays

  - [Эффективные структуры данных для `PHP 7`, представленные как альтернативы для типа `array`](https://www.php.net/manual/ru/book.ds.php)

____

## Requirements

- PHP `7.2` или выше
  - Расширение PHP Pecl DS
  - Класс Ds\Map

## Installation

- Скачайте эту библиотеку и подключите файл в вашем скрипте.
  - Download this library and require the necessary file directly in your script.


``` php
<?php

require __DIR__ . '/path/to/library/ArrayObjectMap.php';
```

## Creation

- Создайте новый класс наследуемый от класса ArrayObjectMap

``` php
<?php

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
```

## Usage

``` php
<?php

$user = new User;

$user->load()
     ->setDima(['id' => 1, 'email' => 'dima@example.com'])
     ->setVasya(['id' => 2, 'email' => 'vasya@example.com'])
     ->setPage
     (
         (new Page)
             ->setTitle('List users')
             ->setDescription('All users site')
             ->setUsers(clone $user)
             ->save()
     )
     ->unsetDima()
     ->unsetVasya();
```

## Доступ к данным

- Пример:

``` php
<?php

foreach ($user->page->users as $name => $value)
{
    echo nl2br(ucfirst($name) .': '.$value->email .PHP_EOL, FALSE);
}
```

- Вернёт:

```
Dima: dima@example.com
Vasya: vasya@example.com
```

- Пример: Получить или удалить

``` php
<?php

$dima = $user->page->users->getDima(); // Получить
$user->page->users->unsetDima(); // удалить
```

- Или так

``` php
<?php

echo $user->page->users->dima->email;
unset($user->page->users->dima->email); // Удалит ключ email
```

- Проанализировать построение данных:

``` php
<?php

var_dump($user);
```

