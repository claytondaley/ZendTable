<?php
/**
 * Created by PhpStorm.
 * User: Clayton Daley
 * Date: 2/21/2015
 * Time: 9:33 PM
 */

namespace DaleyTable;


use Iterator;

class RowIterator implements Iterator
{

    /** @var RowInterface $row_prototype */
    protected $row_prototype = null;

    /** @var Iterator|null $objects */
    protected $iterator = null;

    /**
     * @param RowInterface $row_prototype
     * @param Iterator $iterator
     */
    public function __construct(RowInterface $row_prototype, Iterator $iterator)
    {
        $this->row_prototype = $row_prototype;
        $this->iterator = $iterator;
    }

    /**
     * ====================
     *   Iterator Methods
     * ====================
     * These methods iterate over the array of (row) objects, wrapping each in a clone of the Row Prototype
     */

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        $row = clone $this->row_prototype;
        $row->bind($this->iterator->current());
        return $row;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->iterator->next();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->iterator->rewind();
    }
}