<?php
   /**
  * @todo Methods
  *
  * @uses __callStatic
  * @uses __construct
  * @uses __clone
  * @uses __toString
  * @uses __call
  * @uses __get
  * @uses __set
  * @uses __isset
  * @uses __unset
  * @uses get
  * @uses set
  * @uses isset
  * @uses unset
  * @uses offsetGet
  * @uses offsetSet
  * @uses offsetExists
  * @uses offsetUnset
  * @uses getIterator
  * @uses array
  * @uses default
  * @uses merge
  * @uses load
  * @uses preg_match_callback
  * @uses array_merge_recursive
  */
abstract class ArrayObjectMap implements ArrayAccess, IteratorAggregate {


    /**
     * @access protected
     * @const  string
     */
    protected const PREG_MATCH_REGEX = '/^(get|set|isset|unset)([\w]+)$/iD';


    /**
     * @link https://www.php.net/manual/ru/language.oop5.abstract.php
     * @todo Abstraction
     *
     * @example
     *      // @static
     *      // @access public
     *      // @param  iterable $iterable
     *      // @return object singleton
     *     class Config extends ArrayObjectMap {
     *
     *         public static function instance(iterable $iterable = []): self
     *         {
     *             // @staticvar object singleton
     *             static $singelton;
     *             return ( ! $singelton)
     *                 ? ($singelton = new static($iterable))
     *                 : $singelton;
     *         }
     *     }
     *
     * @static
     * @access public
     * @param  iterable $iterable
     * @return object singleton
     */
    // abstract public static function instance(iterable $iterable = []): self;


    /**
     * @link https://www.php.net/manual/ru/language.oop5.overloading.php#object.callstatic
     * @todo Magic Method __callStatic()
     *
     * @static
     * @access public
     * @param  string $method
     * @param  array  $args
     * @return mixed
     * @throws Exception
     */
    public static function __callStatic($method, $args = []) /** mixed **/
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
     * @throws TypeError
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
     * @throws OutOfBoundsException
     */
    public function __call($method, $args = []) /** mixed **/
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
               $property = strtolower($matches[2]);

               switch($matches[1]):
                   case 'get'  : return $this->get($property);
                   case 'set'  : return $this->set($property, $args[0] ?? NULL);
                   case 'isset': return $this->isset($property);
                   case 'unset':
                   {
                      if ($this->isset($property))
                         return $this->unset([$property, $args[0] ?? FALSE]);
                      else
                      {
                         throw new OutOfBoundsException(
                             'Property: &#171;' .get_called_class() .':: &#36;this&#8211;&#62;' .$property .'&#187; was not found.'
                         );
                      }
                      return NULL;
                   }
                   default : break;
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
     * @param  string|integer|object $key
     * @param  mixed $value
     * @return void
     */
    public function __set($key = NULL, $value = NULL) /** mixed **/
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
     * @example
     *     $charset = $this->unsetTz()     // remove key `tz`
     *                     ->unsetLocale() // remove key `locale`
     *                     ->setLang('en') // set key `lang`
     *                     ->getCharset(); // and return value `charset`
     *
     * @example
     *     echo $this->unsetCharset(TRUE); // remove key `charset` and return value `charset`
     *
     * @access public
     * @param  string|integer $key
     * @return void
     */
    public function __unset($array) /** mixed **/
    {
        // To avoid warning-> Notice: Undefined offset: 1
        list($key, $is_return) = (array) $array;

        if ($key === NULL)
        {
           $this->_changed = [];
           $this->_map
                ->clear();
        }
        else
        {
           $found = array_search($key, $this->_changed);
           if ($found !== FALSE)
           {
              unset($this->_changed[$found]);
           }

           $value = $this->_map
                          ->remove($key);
        }

        return ($is_return)
            ? $value
            : $this;
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
    } public function offsetSet($offset, $value = NULL) {
        return $this->__set($offset, $value);
    } public function offsetExists($offset): bool {
        return $this->__isset($offset);
    } public function offsetUnset($offset = NULL) {
        return $this->__unset($offset);
    }


    /**
     * @access public
     */
    public function & get($key) {
        return $this->__get($key);
    } public function set($key, $value) {
        return $this->__set($key, $value);
    } public function isset($key): bool {
        return $this->__isset($key);
    } public function unset($key = NULL) {
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
     * @example
     *    $config = new Config(
     *        [ 'one' => 1, 'two' => ['three' => '3 ya'] ]
     *    );
     *
     *    $config->default(
     *        ['one' => 7, 'two' => 2, 'three' => 3]
     *    );
     *
     *    var_dump array(3)
     *    {
     *        ["one"] => 1
     *        ["two"] => array(1)
     *        {
     *            ["three"] => "3 ya"
     *        }
     *        ["three"] => 3
     *    }
     *
     * @access public
     * @param  iterable|string $merge
     * @return self
     */
    public function default($merge = []): self
    {
        $merge = $this->array_merge_recursive
        (
            (is_array($merge)
                ? $merge
                : ($merge instanceof static
                      ? $merge->array()
                      : ($merge instanceof ArrayObject
                            ? $merge->getArrayCopy()
                            : (array) $merge
                        )
                  )
            ),
            $this->array()
        );

        foreach ($merge as $key => $value)
        {
            $this->__set($key, $value);
        }

        return $this;
    }


    /**
     * @example
     *    $config = new Config(
     *        [ 'one' => 1, 'two' => ['three' => '3 ya'] ]
     *    );
     *
     *    $config->default(
     *        ['one' => 7, 'two' => 2, 'three' => 3]
     *    );
     *
     *    var_dump array(3)
     *    {
     *        ["one"] => 7
     *        ["two"] => 2
     *        ["three"] => 3
     *    }
     *
     * @access public
     * @param  iterable|string $merge
     * @return self
     */
    public function merge($merge = []): self
    {
        $merge = $this->array_merge_recursive
        (
            $this->array(),

            (is_array($merge)
                ? $merge
                : ($merge instanceof static
                      ? $merge->array()
                      : ($merge instanceof ArrayObject
                            ? $merge->getArrayCopy()
                            : (array) $merge
                        )
                  )
            )
        );

        foreach ($merge as $key => $value)
        {
            $this->__set($key, $value);
        }

        return $this;
    }


    /**
     * @example
     *     $config = (new Config)->load('/var/path/config.php');
     *
     *     $config->load(
     *         [
     *             '/var/path/config.php',
     *             '/var/path2/config_merge.php'
     *         ]
     *     );
     *
     * @access public
     * @param  array|string  $paths
     * @return self
     */
    public function load($load = []): self
    {
        if ($load)
        {
           foreach ((array) $load as $path)
           {
               $this->merge((array) require $path);
           }
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
     * @param  array $array initial array
     * @param  array $merge array to merge
     * @return array
     */
    public function array_merge_recursive(array $array, array $merge): array
    {
        if (array_keys(($_ = array_keys($merge))) !== $_) // is_assoc
        {
           foreach ($merge as $key => $value)
               $array[$key] = (is_array($value) && isset($array[$key]) && is_array($array[$key]))
                   ? $this->array_merge_recursive($array[$key], $value)
                   : $value;
        }
        else
        {
            foreach ($merge as $value)
            {
                if ( ! in_array($value, $array, TRUE))
                   $array[] = $value;
            }
        }
        return $array;
    }
}


// End

