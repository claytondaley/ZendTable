<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace DaleyTable\View\Helper;

use Zend\Form\View\Helper\AbstractHelper;
use DaleyTable\RowInterface;
use DaleyTable\TableInterface;

/**
 * View helper for rendering Form objects
 */
class Table extends AbstractHelper
{
    /**
     * Instance map to view helper
     *
     * @var array
     */
    protected $classMap = array(
        'DaleyTable\StaticRow' => 'tablestaticrow',
    );

    /**
     * Type map to view helper
     *
     * @var array
     */
    protected $typeMap = array(
        'staticrow' => 'tablestaticrow',
    );

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
     * @param  null|TableInterface $table
     * @return Table
     */
    public function __invoke(TableInterface $table = null)
    {
        if (!$table) {
            return $this;
        }

        return $this->render($table);
    }

    /**
     * Render a form from the provided $form,
     *
     * @param  TableInterface $table
     * @return string
     */
    public function render(TableInterface $table)
    {
        if (method_exists($table, 'prepare')) {
            $table->prepare();
        }

        $tableContent = '';

        $tableContent.= $this->getView()->tableHeaderRow($table->getRowPrototype());

        foreach ($table as $row) {
            $renderedInstance = $this->renderInstance($row);

            if ($renderedInstance !== null) {
                $tableContent.= $renderedInstance;
                continue;
            }

            $renderedType = $this->renderType($row);

            if ($renderedType !== null) {
                $tableContent.= $renderedType;
                continue;
            }

            return $this->renderHelper($this->defaultHelper, $row);
        }

        return $this->openTag($table) . $tableContent . $this->closeTag();
    }

    /**
     * Generate an opening form tag
     *
     * @param  null|TableInterface $table
     * @return string
     */
    public function openTag(TableInterface $table = null)
    {
        $attributes = array();

        if ($table instanceof TableInterface) {
            $tableAttributes = $table->getAttributes();
            if (!array_key_exists('id', $tableAttributes) && array_key_exists('name', $tableAttributes)) {
                $tableAttributes['id'] = $tableAttributes['name'];
            }
            $attributes = array_merge($attributes, $tableAttributes);
        }

        return sprintf('<table %s>', $this->createAttributesString($attributes));
    }

    /**
     * Generate a closing form tag
     *
     * @return string
     */
    public function closeTag()
    {
        return '</table>';
    }

    /**
     * Render element by helper name
     *
     * @param string $name
     * @param RowInterface $row
     * @return string
     */
    protected function renderHelper($name, RowInterface $row)
    {
        $helper = $this->getView()->plugin($name);
        return $helper($row);
    }

    /**
     * Render element by instance map
     *
     * @param RowInterface $row
     * @return string|null
     */
    protected function renderInstance(RowInterface $row)
    {
        foreach ($this->classMap as $class => $pluginName) {
            if ($row instanceof $class) {
                return $this->renderHelper($pluginName, $row);
            }
        }
        return null;
    }

    /**
     * Render element by type map
     *
     * @param RowInterface $row
     * @return string|null
     */
    protected function renderType(RowInterface $row)
    {
        $type = $row->getAttribute('type');

        if (isset($this->typeMap[$type])) {
            return $this->renderHelper($this->typeMap[$type], $row);
        }
        return null;
    }
}
