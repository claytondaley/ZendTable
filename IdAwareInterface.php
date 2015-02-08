<?php
/**
 * Created by PhpStorm.
 * User: Clayton Daley
 * Date: 2/15/2015
 * Time: 10:02 PM
 */

namespace DaleyTable;

interface IdAwareInterface {
    /**
     * Set the field that will provide the key
     *
     * @param $id string
     * @return mixed
     */
    public function setId($id);

    /**
     * Get the filed that will provide the key
     *
     * @return string|null
     */
    public function getId();

}