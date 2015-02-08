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
use Zend\Form\View\Helper\FormButton;
use DaleyTable\Element\Link;

class FormLink extends FormButton
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
        'href'           => true,
        'form'           => true,
        'formaction'     => true,
        'formenctype'    => true,
        'formmethod'     => true,
        'formnovalidate' => true,
        'formtarget'     => true,
        'type'           => true,
    );

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
        if (!$attributesOrElement instanceof Link) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s requires that the element is of type DaleyTable\Element\Link',
                __METHOD__
            ));
        }

        if (null === $attributesOrElement) {
            return '';
        }

        if (is_array($attributesOrElement)) {
            $attributes = $this->createAttributesString($attributesOrElement);
            return sprintf('<a %s>', $attributes);
        }

        if (!$attributesOrElement instanceof ElementInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Zend\Form\ElementInterface instance; received "%s"',
                __METHOD__,
                (is_object($attributesOrElement) ? get_class($attributesOrElement) : gettype($attributesOrElement))
            ));
        }

        /** @var Link $element */
        $element = $attributesOrElement;
        $name    = $element->getName();
        if (empty($name) && $name !== 0) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $attributes          = $element->getAttributes();
        $attributes['name']  = $name;

        // set href
        $href = $element->getValue();
        // caller has provided route using idLinkRoute
        if ($element->getOption('idLinkRoute') !== null) {
            $key = ($element->getOption('idLinkParam') ?: 'id');
            $href = $this->getView()->url(
                $element->getOption('idLinkRoute'),
                array($key => $element->getId())
            );
        }
        // caller has provided url using idLinkParam
        elseif ($element->getOption('idLinkUrl')) {
            $key = ($element->getOption('idLinkParam') ?: 'id');
            $href = $element->getOption('idLinkUrl');
            if (strpos($href, '?')) {
                $href.= '&' . $key . '=' . $element->getId();
            } else {
                $href.= '?' . $key . '=' . $element->getId();
            }
        }

        $attributes['href'] = $href;

        return sprintf(
            '<a %s>',
            $this->createAttributesString($attributes)
        );
    }

    /**
     * Return a closing button tag
     *
     * @return string
     */
    public function closeTag()
    {
        return '</a>';
    }
}
