<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace DaleyTable\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\Exception;
use Zend\Form\View\Helper\AbstractHelper;
use DaleyTable\RowInterface;

class TableStaticRow extends AbstractHelper
{
    /**
     * Form element helper instance
     *
     * @var AbstractHelper
     */
    protected $elementHelper;

    /**
     * @var string
     */
    protected $partial;

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @param  null|ElementInterface $element
     * @param  string|null           $partial
     * @return string|TableStaticRow
     */
    public function __invoke(ElementInterface $element = null, $partial = null)
    {
        if (!$element) {
            return $this;
        }

        return $this->render($element);
    }

    /**
     * Utility form helper that renders a row
     *
     * @param  RowInterface $row
     * @throws \Zend\Form\Exception\DomainException
     * @return string
     */
    public function render(RowInterface $row)
    {
        if (method_exists($row, 'prepare')) {
            $row->prepare();
        }

        $tableContent = '';

        $attributes = $row->getAttributes();
        // Attach standardized classes for formatting
        $attributes['class'] = ($attributes['class'] ? $attributes['class'] . ' row' : 'row');
        if ($row->getId() !== null) {
            $attributes['class'].= sprintf(' row-%s', $row->getId());
        }
        $tableContent.= sprintf('<tr %s>', $this->createAttributesString($attributes));

        foreach ($row as $element) {
            $tableContent.= $this->getView()->tableStaticCell($element);
        }

        $tableContent.= '</tr>';
        return $tableContent;
    }
}
