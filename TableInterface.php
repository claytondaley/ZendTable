<?php
/**
 * Created by PhpStorm.
 * User: Clayton Daley
 * Date: 2/7/2015
 * Time: 12:12 PM
 */

namespace DaleyTable;

use Zend\Form\FieldsetInterface;
use Zend\Stdlib\InitializableInterface;

interface TableInterface extends FieldsetInterface, InitializableInterface
{

    /**
     * @param RowInterface $row_prototype
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setRowPrototype(RowInterface $row_prototype);

    /**
     * @return RowInterface
     * @throws Exception\InvalidArgumentException
     */
    public function getRowPrototype();

    /**
     * ==============================================
     *   Proxies for prototype RowInterface methods
     * ==============================================
     */

    /**
     * Retrieve all attached elements
     *
     * Storage is an implementation detail of the concrete class.
     *
     * @return array|\Traversable
     */
    public function getElements();

}