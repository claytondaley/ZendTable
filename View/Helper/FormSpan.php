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

class FormSpan extends AbstractHelper
{
    /**
     * Attributes valid for the button tag
     *
     * @var array
     */
    protected $validTagAttributes = array(
        'name'           => true,
        'autofocus'      => true,
        'disabled'       => true,
        'form'           => true,
        'formaction'     => true,
        'formenctype'    => true,
        'formmethod'     => true,
        'formnovalidate' => true,
        'formtarget'     => true,
        'type'           => true,
        'value'          => true,
    );

    protected $nativeHandlers = array(
        // The key is all that matters
        'Zend\Form\Element\Button'    => null,
        'DaleyTable\Element\Link'      => null,
    );

    /**
     * Valid values for the link type
     *
     * @var array
     */
    protected $validTypes = array(
        'element' => true,
    );

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @param  ElementInterface|null $element
     * @return string|FormLink
     */
    public function __invoke(ElementInterface $element = null)
    {
        if (!$element) {
            return $this;
        }

        return $this->render($element);
    }

    /**
     * Render a form <button> element from the provided $element,
     * using content from $buttonContent or the element's "label" attribute
     *
     * @param  ElementInterface $elementOrCollection
     * @throws Exception\DomainException
     * @return string
     */
    public function render(ElementInterface $elementOrCollection)
    {
        $cellContent = '';

        if ($elementOrCollection instanceof FieldsetInterface) {
            $spans = array();
            foreach ($elementOrCollection as $element) {
                $spans[] = $this->getView()->formSpan($element);
            }
            $delimiter = ($elementOrCollection->getOption('delimiter')?:"");
            $cellContent.= implode($delimiter, $spans);
        } else {
            // Buttons (and links) need to be rendered natively
            $class_ = get_class($elementOrCollection);
            if ($this->getRenderNatively($class_)) {
                // When integrated, handlers for "Link" should be added to FormElement
                $cellContent.= $this->getView()->formElement($elementOrCollection);
            } else {
                $attributes     = $elementOrCollection->getAttributes();
                $name           = $elementOrCollection->getName();
                if ($name) {
                    $attributes['name'] = $name;
                    // Attach standardized classes for formatting
                    $classAttribute = ($attributes['class'] ? $attributes['class'] . ' ' : '');
                    $attributes['class'] = $classAttribute . sprintf('span-%s', $elementOrCollection->getName());
                }

                $cellContent.= $this->openTag($attributes);

                $body = $elementOrCollection->getValue();

                if (! $elementOrCollection->getLabelOption('disable_html_escape')) {
                    $escapeHtmlHelper = $this->getEscapeHtmlHelper();
                    $body = $escapeHtmlHelper($body);
                }

                $cellContent.= $body;

                $cellContent.= $this->closeTag();
            }
        }

        return $cellContent;
    }

    /**
     * Generate an opening button tag
     *
     * @param  null|array|ElementInterface $attributesOrElement
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     * @return string
     */
    public function openTag($attributesOrElement = null)
    {
        if (null === $attributesOrElement) {
            return '<span>';
        }

        if (is_array($attributesOrElement)) {
            $attributes = $this->createAttributesString($attributesOrElement);
            return sprintf('<span %s>', $attributes);
        }

        if (!$attributesOrElement instanceof ElementInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Zend\Form\ElementInterface instance; received "%s"',
                __METHOD__,
                (is_object($attributesOrElement) ? get_class($attributesOrElement) : gettype($attributesOrElement))
            ));
        }

        $element = $attributesOrElement;
        $name    = $element->getName();
        if (empty($name) && $name !== 0) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $attributes         = $element->getAttributes();
        $attributes['name'] = $element->getName();

        return sprintf(
            '<span %s>',
            $this->createAttributesString($attributes)
        );
    }

    /**
     * Return a closing link tag
     *
     * @return string
     */
    public function closeTag()
    {
        return '</span>';
    }

    /**
     * @param string $class_
     * @return bool
     */
    public function getRenderNatively($class_)
    {
        return array_key_exists($class_, $this->nativeHandlers);
    }

    /**
     * @param string $class_
     * @param bool $native
     * @return TableStaticCell
     */
    public function setRenderNatively($class_, $native)
    {
        if ($native) {
            $this->nativeHandlers[$native] = null;
        } else {
            unset($this->nativeHandlers[$class_]);
        }

        return $this;
    }
}
