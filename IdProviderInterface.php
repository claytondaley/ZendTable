<?php
/**
 * Created by PhpStorm.
 * User: Clayton Daley
 * Date: 2/15/2015
 * Time: 10:02 PM
 */

namespace DaleyTable;

interface IdProviderInterface {
    /**
     * Set the field that will provide the key
     *
     * @param $id string|null
     * @return mixed
     */
    public function setIdField($id);

    /**
     * Get the filed that will provide the key
     *
     * @return string|null
     */
    public function getIdField();

}