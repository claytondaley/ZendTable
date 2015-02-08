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
use Zend\Form\FieldsetInterface;
use Zend\Form\View\Helper\AbstractHelper;

class TableStaticCell extends AbstractHelper
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
     * @return string|TableStaticCell
     */
    public function __invoke(ElementInterface $element = null, $partial = null)
    {
        if (!$element) {
            return $this;
        }

        if ($partial !== null) {
            $this->setPartial($partial);
        }

        return $this->render($element);
    }

    /**
     * Utility form helper that renders a row
     *
     * @param  ElementInterface $elementOrCollection
     * @throws \Zend\Form\Exception\DomainException
     * @return string
     */
    public function render(ElementInterface $elementOrCollection)
    {
        if (method_exists($elementOrCollection, 'prepare')) {
            $elementOrCollection->prepare();
        }

        $cellContent = '';

        if ($elementOrCollection instanceof FieldsetInterface && $elementOrCollection->getOption('expand') === true) {
            // Each element gets its own column
            foreach ($elementOrCollection as $element) {
                $cellContent.= $this->getView()->tableStaticCell($element);
            }
        } else {
            $attributes = $elementOrCollection->getAttributes();
            // Attach standardized classes for formatting
            if ($elementOrCollection->getName()) {
                $classAttribute = ($attributes['class'] ? $attributes['class'] . ' ' : '');
                $attributes['class'] = $classAttribute . sprintf('cell cell-%s', $elementOrCollection->getName());
            }
            $cellContent.= sprintf('<td %s>', $this->createAttributesString($attributes));

            $cellContent.= $this->getView()->formSpan($elementOrCollection);

            $cellContent.= '</td>';
        }

        return $cellContent;
    }
}
