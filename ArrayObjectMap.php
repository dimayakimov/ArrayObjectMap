<?php

abstract class ArrayObjectMap implements ArrayAccess, IteratorAggregate {


    /**
     * @access public
     * @const  string
     */
    public const PREG_CAMEL_CASE = '/(?<=[A-Z])(?=[A-Z][a-z])|(?<=[^A-Z])(?=[A-Z])|(?<=[A-Za-z])(?=[^A-Za-z])/D';

    /**
     * @access public
     * @const  string
     */
    public const PREG_MATCH_REGEX = '/^(get|set|isset|unset)([\w]+)$/D';


    /**
     * @link https://www.php.net/manual/ru/language.oop5.overloading.php#object.callstatic
     * @todo Magic Method __callStatic()
     *
     * @static
     * @access public
     * @param  string $method
     * @param  array  $args
     * @return mixed
     * @throw  Exception
     */
    public static function __callStatic($method, array $args = []) /** mixed **/
    {
        try
        {
            /**
             * @link https://www.php.net/manual/ru/language.exceptions.php
             * @todo Exception
             */
            throw new Exception(
                'Magic method: &#171;' .get_called_class() .'::' .__FUNCTION__ .'()&#187;  is empty.'
            );
        }
        catch (Exception $e)
        {
            echo nl2br(
                $e->getMessage() .' Line: ' .$e->getLine() .' File: &#8230;/' .basename($e->getFile()) .PHP_EOL
                , FALSE
            );
        }

        return NULL;
    }


    /**
     * @access protected
     * @var    object \Ds\Map
     */
    protected $_map;

    /**
     * @access protected
     * @var    array
     */
    protected array $_changed = [];


    /**
     * @link https://www.php.net/manual/en/language.oop5.decon.php
     * @todo Constructor
     *
     * @access public
     * @link https://www.php.net/manual/ru/language.types.iterable.php
     * @param  iterable $iterable
     * @return no type
     * @throw  TypeError
     */
    public function __construct(iterable $iterable = []) /** no type **/
    {
         /**
          * @link https://www.php.net/manual/ru/class.ds-map.php
          */
         $this->_map = new \Ds\Map($iterable);

         foreach ($iterable as $key => $value)
         {
             $this->__set($key, $value);
         }
    }


    /**
     * @link https://www.php.net/manual/ru/language.oop5.cloning.php#object.clone
     * @todo Magic Method __clone()
     *
     * @access public
     * @return void
     */
    public function __clone()
    {
        $this->_map = clone $this->_map;
    }


    /**
     * @link https://www.php.net/manual/ru/language.oop5.magic.php#object.tostring
     * @todo Magic Method __toString()
     *
     * @access public
     * @return string
     */
    public function __toString(): string
    {
        return serialize($this);
    }


    /**
     * @link https://www.php.net/manual/ru/language.oop5.overloading.php#object.call
     * @todo Magic Method __call()
     *
     * @access public
     * @param  string $method
     * @param  array  $args
     * @return mixed
     * @throw  OutOfBoundsException
     */
    public function __call($method, array $args = []) /** mixed **/
    {
        try
        {
            if ( ! $this->preg_match_callback(self::PREG_MATCH_REGEX, $method, 'method_exists', $matches))
            {
               /**
                * @link https://www.php.net/manual/ru/class.outofboundsexception.php
                * @todo OutOfBoundsException
                */
               throw new OutOfBoundsException(
                   'Method: &#171;' .get_called_class() .':: &#36;this&#8211;&#62;' .$method .'()&#187; was not found.'
               );
            }
            else
            {
               $key = strtolower(
                   preg_replace(self::PREG_CAMEL_CASE, '_$0', $matches[2])
               );

               switch($matches[1]):
                   case 'get'  : return $this->get($key);
                   case 'set'  : return $this->set($key, $args[0] ?? NULL);
                   case 'isset': return $this->isset($key);
                   case 'unset': return $this->unset($key);
                   default     : break;
               endswitch;
            }
        }
        catch (OutOfBoundsException $e)
        {
            echo nl2br(
                $e->getMessage() .' Line: ' .$e->getLine() .' File: &#8230;/' .basename($e->getFile()) .PHP_EOL
                , FALSE
            );

            return NULL;
        }

        return $this;
    }


    /**
     * @link https://www.php.net/manual/ru/language.oop5.overloading.php#object.get
     * @todo Magic Method __get()
     *
     * @access public
     * @param  string|integer|object $key
     * @return mixed
     */
    public function & __get($key) /** mixed **/
    {
        return $this->_map
                     ->get($key, NULL);
    }


    /**
     * @link https://www.php.net/manual/ru/language.oop5.overloading.php#object.set
     * @todo Magic Method __set()
     *
     * @access public
     * @param  string|integer $key
     * @param  mixed $value
     * @return void
     */
    public function __set($key = NULL, $value = NULL)
    {
        $value = (is_array($value))
            ? new static($value)
            : $value;

        if ($key === NULL)
           $this->_map
                ->putAll([$value]);
        elseif ($value !== NULL)
        {
           if ( ! in_array($key, $this->_changed))
           {
              $this->_changed[] = $key;
           }

           $this->_map
                ->put($key, $value);
        }

        return $this;
    }


    /**
     * @link https://www.php.net/manual/ru/language.oop5.overloading.php#object.isset
     * @todo Magic Method __isset()
     *
     * @access public
     * @param  string|integer $key
     * @return boolean
     */
    public function __isset($key): bool
    {
        return $this->_map
                    ->hasKey($key);
    }


    /**
     * @link https://www.php.net/manual/ru/language.oop5.overloading.php#object.unset
     * @todo Magic Method __unset()
     *
     * @access public
     * @param  string|integer $key
     * @return void
     */
    public function __unset($key = NULL)
    {
        if ($key === NULL)
        {
           $this->_changed = [];
           $this->_map
                ->clear();
        }
        else
        {
           $search_key = array_search($key, $this->_changed);
           if ($search_key !== FALSE)
           {
              unset($this->_changed[$search_key]);
           }

           ! $this->__isset($key)
               || $this->_map
                       ->remove($key);
        }

        return $this;
    }


    /**
     * @link https://www.php.net/manual/ru/class.iteratoraggregate.php
     * @todo IteratorAggregate
     *
     * @access public
     * @return object ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator
        (
            $this->_map->toArray()
        );
    }


    /**
     * @link https://www.php.net/manual/en/class.arrayaccess.php
     * @todo ArrayAccess offsetGet offsetSet offsetExists offsetUnset ...
     *
     * @access public
     */
    public function & offsetGet($offset) {
        return $this->__get($offset);
    }
    public function offsetSet($offset, $value = NULL) {
        return $this->__set($offset, $value);
    }
    public function offsetExists($offset): bool {
        return $this->__isset($offset);
    }
    public function offsetUnset($offset = NULL) {
        return $this->__unset($offset);
    }


    /**
     * @access public
     */
    public function & get($key) {
        return $this->__get($key);
    }
    public function set($key, $value) {
        return $this->__set($key, $value);
    }
    public function isset($key): bool {
        return $this->__isset($key);
    }
    public function unset($key = NULL) {
        return $this->__unset($key);
    }


    /**
     * @access public
     * @param  array $array
     * @return array
     */
    public function array(array $array = []): array
    {
        foreach ($this->_map as $key => $value)
        {
           $array[$key] = ($value instanceof static)
               ? $value->array()
               : $value;
        }

        return $array;
    }


    /**
     * @access public
     * @param  mixed $mixed
     * @return self
     */
    public function default($mixed = []): self
    {
        $mixed = $this->array_merge_recursive
        (
            (is_array($mixed)
                ? $mixed
                : ($mixed instanceof static
                      ? $mixed->array()
                      : ($mixed instanceof ArrayObject
                            ? $mixed->getArrayCopy()
                            : (array) $mixed
                        )
                  )
            ),
            $this->array()
        );

        foreach ($mixed as $key => $value)
        {
            $this->__set($key, $value);
        }

        return $this;
    }


    /**
     * @access public
     * @param  mixed $mixed
     * @return self
     */
    public function merge($mixed = []): self
    {
        $mixed = $this->array_merge_recursive
        (
            $this->array(),

            (is_array($mixed)
                ? $mixed
                : ($mixed instanceof static
                      ? $mixed->array()
                      : ($mixed instanceof ArrayObject
                            ? $mixed->getArrayCopy()
                            : (array) $mixed
                        )
                  )
            )
        );

        foreach ($mixed as $key => $value)
        {
            $this->__set($key, $value);
        }

        return $this;
    }


    /**
     * @link https://www.php.net/manual/ru/function.preg-replace-callback.php
     * @todo preg_match_callback()
     *
     * @final
     * @access public
     * @param  string   $regex
     * @param  string   $subject
     * @param  callable $callback
     * @param  array  & $matches
     * @return boolean
     */
    final public function preg_match_callback($regex, $subject, $callback, & $matches): bool
    {
        $matches = explode("\0",
            preg_replace_callback($regex,
                function($matches) use ($callback)
                {
                    return call_user_func($callback, get_called_class(), $matches[1])
                        ? implode("\0", $matches)
                        : NULL;
                }
                , $subject
           )
       );

       return count($matches) > 1;
    }


    /**
     * @link https://www.php.net/manual/ru/function.array-merge-recursive.php
     *
     * @package   Kohana
     * @category  Helpers
     * @author    Kohana Team
     * @copyright (c) 2007-2012 Kohana Team
     * @license   http://kohanaframework.org/license
     *
     * @access public
     * @param  array $array1 initial array
     * @param  array $array2 array to merge
     * @return array
     */
    public function array_merge_recursive(array $array1, array $array2): array
    {
        if (array_keys(($_ = array_keys($array2))) !== $_) // is_assoc
        {
           foreach ($array2 as $key => $value)
               $array1[$key] = (is_array($value) && isset($array1[$key]) && is_array($array1[$key]))
                   ? $this->array_merge_recursive($array1[$key], $value)
                   : $value;
        }
        else
        {
            foreach ($array2 as $value)
            {
                if ( ! in_array($value, $array1, TRUE))
                   $array1[] = $value;
            }
        }
        return $array1;
    }


    /**
     * @link https://www.php.net/manual/ru/language.oop5.abstract.php
     * @todo Abstraction
     *
     * @access public
     */
    //abstract public function load();
    //abstract public function create();
    //abstract public function save();
    //abstract public function unload();
    //abstract public function remove();
}


// End

