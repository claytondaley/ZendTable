<?php
/**
 * Created by PhpStorm.
 * User: Clayton Daley
 * Date: 2/15/2015
 * Time: 10:04 PM
 */

namespace DaleyTable;


use Zend\Form\Element\Collection;

class KeyedCollection extends Collection
    implements IdAwareInterface
{
    protected $recordId = null;

    /**
     * Store the key (and relay to children)
     *
     * @param $id string|integer
     * @return mixed
     */
    public function setId($id)
    {
        $this->recordId = $id;
        foreach ($this as $element) {
            if ($element instanceof IdAwareInterface) {
                $element->setId($id);
            }
        }

        return $this;
    }

    /**
     * Get the filed that will provide the key
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->recordId;
    }
}