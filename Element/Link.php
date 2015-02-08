<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace DaleyTable\Element;

use Zend\Form\Element;
use DaleyTable\IdAwareInterface;

class Link extends Element
    implements IdAwareInterface
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'link',
    );

    protected $recordId = null;

    public function setId($id)
    {
        $this->recordId = $id;

        return $this;
    }

    public function getId()
    {
        return $this->recordId;
    }
}
