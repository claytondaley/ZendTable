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
use Zend\Form\FieldsetInterface;
use Zend\Form\View\Helper\AbstractHelper;

/**
 * View helper for rendering Form objects
 */
class TableHeaderCell extends AbstractHelper
{
    /**
     * Attributes valid for this tag (form)
     *
     * @var array
     */
    protected $validTagAttributes = array(
        'accept-charset' => true,
        'action'         => true,
        'autocomplete'   => true,
        'enctype'        => true,
        'method'         => true,
        'name'           => true,
        'novalidate'     => true,
        'target'         => true,
    );

    /**
     * Invoke as function
     *
     * @param  null|ElementInterface $element
     * @return Table
     */
    public function __invoke(ElementInterface $element = null)
    {
        if (!$element) {
            return $this;
        }

        return $this->render($element);
    }

    /**
     * Render a row from the provided $form,
     *
     * @param  ElementInterface $elementOrCollection
     * @return string
     */
    public function render(ElementInterface $elementOrCollection)
    {
        if (!$elementOrCollection) {
            return $this;
        }

        if (method_exists($elementOrCollection, 'prepare')) {
            $elementOrCollection->prepare();
        }

        $headerContent = '';

        if ($elementOrCollection instanceof FieldsetInterface && $elementOrCollection->getOption('expand') === true) {
            // If this is a collection *and* has the "expand" option set
            // to (bool) true, we put each child in its own column.
            foreach ($elementOrCollection as $element) {
                $headerContent.= $this->getView()->tableHeaderCell($element);
            }
        } else {
            // Either this is a single element or the collection is to
            // be displayed in the same column.  Implicitly, the child
            // elements will also be rendered in a single cell
            $cellContent = '';
            $attributes = $elementOrCollection->getAttributes();
            // Attach standardized classes for formatting
            if ($elementOrCollection->getName()) {
                $classAttribute = ($attributes['class'] ? $attributes['class'] . ' ' : '');
                $attributes['class'] = $classAttribute . sprintf(' header header-%s', $elementOrCollection->getName());
            }
            $cellContent.= sprintf('<th %s>', $this->createAttributesString($attributes));

            $label = $elementOrCollection->getLabel();
            if (isset($label) && '' !== $label) {
                // Translate the label
                if (null !== ($translator = $this->getTranslator())) {
                    $label = $translator->translate($label, $this->getTranslatorTextDomain());
                }
                // HTML escape the label
                if (! $elementOrCollection->getLabelOption('disable_html_escape')) {
                    $escapeHtmlHelper = $this->getEscapeHtmlHelper();
                    $label = $escapeHtmlHelper($label);
                }

                $cellContent.= $label;
            }
            $cellContent.= '</th>';
            $headerContent.= $cellContent;
        }
        return $headerContent;
    }
}
