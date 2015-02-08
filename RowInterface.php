<?php
/**
 * Created by PhpStorm.
 * User: Clayton Daley
 * Date: 2/6/2015
 * Time: 10:31 AM
 */

namespace DaleyTable;

use Zend\Form\FieldsetInterface;

interface RowInterface extends FieldsetInterface
{
    /**
     * Set options for a form. Accepted options are:
     * - prefer_form_input_filter: is form input filter is preferred?
     *
     * @param  array|Traversable $options
     * @return self
     * @throws Exception\InvalidArgumentException
     */
    public function setOptions($options);

    /**
     * Ensures state is ready for use
     *
     * Marshalls the input filter, to ensure validation error messages are
     * available, and prepares any elements and/or fieldsets that require
     * preparation.
     *
     * @return self
     */
    public function prepare();

    /**
     * Bind an object to the element
     *
     * Allows populating the object with validated values.
     *
     * @param  object $object
     * @return mixed
     */
    public function bind($object);

    /**
     * Set the base fieldset to use when hydrating
     *
     * @param  FieldsetInterface $baseFieldset
     * @return self
     * @throws Exception\InvalidArgumentException
     */
    public function setBaseFieldset(FieldsetInterface $baseFieldset);

    /**
     * Get the base fieldset to use when hydrating
     *
     * @return FieldsetInterface
     */
    public function getBaseFieldset();

    /**
     * Are the form elements/fieldsets names wrapped by the form name ?
     *
     * @param  bool $wrapElements
     * @return self
     */
    public function setWrapElements($wrapElements);

    /**
     * If true, form elements/fieldsets name's are wrapped around the form name itself
     *
     * @return bool
     */
    public function wrapElements();
}
