<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace DaleyTable\View\Helper;

use Zend\Form\FieldsetInterface;
use Zend\Form\View\Helper\AbstractHelper;

/**
 * View helper for rendering Form objects
 */
class TableHeaderRow extends AbstractHelper
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
     * @param  null|FieldsetInterface $row
     * @return Table
     */
    public function __invoke(FieldsetInterface $row = null)
    {
        if (!$row) {
            return $this;
        }

        return $this->render($row);
    }

    /**
     * Render a row from the provided $form,
     *
     * @param  FieldsetInterface $row
     * @return string
     */
    public function render(FieldsetInterface $row)
    {
        if (method_exists($row, 'prepare')) {
            $row->prepare();
        }

        $tableContent = '';

        // Create opening tag
        $attributes = $row->getAttributes();
        // Attach standardized classes for formatting
        $classAttribute = ($attributes['class'] ? $attributes['class'] . ' ' : '');
        $attributes['class'] = $classAttribute . 'row header-row';
        $tableContent .= sprintf('<tr %s>', $this->createAttributesString($attributes));

        foreach ($row as $element) {
            $tableContent.= $this->getView()->tableHeaderCell($element);
        }

        $tableContent.= '</tr>';
        return $tableContent;
    }
}
