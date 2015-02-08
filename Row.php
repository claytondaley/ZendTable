<?php
/**
 * Created by PhpStorm.
 * User: Clayton Daley
 * Date: 2/15/2015
 * Time: 5:41 PM
 */

namespace DaleyTable;

use Traversable;
use Zend\Form\ElementInterface;
use Zend\Form\ElementPrepareAwareInterface;
use Zend\Form\Factory;
use Zend\Form\Fieldset;
use Zend\Form\FieldsetInterface;
use Zend\Form\FormInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

abstract class Row extends Fieldset
    implements RowInterface, IdProviderInterface
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
    );

    /**
     * Base fieldset to use for hydrating (if none specified, directly hydrate elements)
     *
     * @var FieldsetInterface
     */
    protected $baseFieldset;

    /**
     * Is the form prepared ?
     *
     * @var bool
     */
    protected $isPrepared = false;

    /**
     * Are the form elements/fieldsets wrapped by the form name ?
     *
     * @var bool
     */
    protected $wrapElements = false;

    /**
     * Set options for a row. Accepted options are:
     *
     * @param  array|Traversable $options
     * @return self
     * @throws Exception\InvalidArgumentException
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($options['prefer_form_input_filter'])) {
            $this->setPreferFormInputFilter($options['prefer_form_input_filter']);
        }

        if (isset($options['use_input_filter_defaults'])) {
            $this->setUseInputFilterDefaults($options['use_input_filter_defaults']);
        }

        return $this;
    }

    /**
     * Add an element or fieldset
     *
     * If $elementOrFieldset is an array or Traversable, passes the argument on
     * to the composed factory to create the object before attaching it.
     *
     * $flags could contain metadata such as the alias under which to register
     * the element or fieldset, order in which to prioritize it, etc.
     *
     * @param  array|Traversable|ElementInterface $elementOrFieldset
     * @param  array                              $flags
     * @return self
     */
    public function add($elementOrFieldset, array $flags = array())
    {
        // TODO: find a better solution than duplicating the factory code, the problem being that if $elementOrFieldset is an array,
        // it is passed by value, and we don't get back the concrete ElementInterface
        if (is_array($elementOrFieldset)
            || ($elementOrFieldset instanceof Traversable && !$elementOrFieldset instanceof ElementInterface)
        ) {
            $factory = $this->getFormFactory();
            $elementOrFieldset = $factory->create($elementOrFieldset);
        }

        parent::add($elementOrFieldset, $flags);

        if ($elementOrFieldset instanceof Fieldset && $elementOrFieldset->useAsBaseFieldset()) {
            $this->baseFieldset = $elementOrFieldset;
        }

        return $this;
    }

    /**
     * Ensures state is ready for use
     *
     * Marshalls the input filter, to ensure validation error messages are
     * available, and prepares any elements and/or fieldsets that require
     * preparation.
     *
     * @return self
     */
    public function prepare()
    {
        if ($this->isPrepared) {
            return $this;
        }

        // If the user wants to, elements names can be wrapped by the form's name
        if ($this->wrapElements()) {
            $this->prepareElement($this);
        } else {
            foreach ($this->getIterator() as $elementOrFieldset) {
                if ($elementOrFieldset instanceof RowInterface) {
                    $elementOrFieldset->prepare();
                } elseif ($elementOrFieldset instanceof ElementPrepareAwareInterface) {
                    $elementOrFieldset->prepareElement($this);
                }
            }
        }

        $this->isPrepared = true;
        return $this;
    }

    /**
     * Ensures state is ready for use. Here, we append the name of the fieldsets to every elements in order to avoid
     * name clashes if the same fieldset is used multiple times
     *
     * @param  FieldsetInterface $form
     * @return mixed|void
     */
    public function prepareElement(FieldsetInterface $form)
    {
        $name = $this->getName();

        foreach ($this->byName as $elementOrFieldset) {
            if ($form->wrapElements()) {
                $elementOrFieldset->setName($name . '[' . $elementOrFieldset->getName() . ']');
            }

            // Recursively prepare elements
            if ($elementOrFieldset instanceof ElementPrepareAwareInterface) {
                $elementOrFieldset->prepareElement($form);
            }
        }
    }

    /**
     * Bind an object to the form
     *
     * Ensures the object is populated with validated values.
     *
     * @param  object $object
     * @param  int $flags
     * @return self
     * @throws Exception\InvalidArgumentException
     */
    public function bind($object, $flags = FormInterface::VALUES_NORMALIZED)
    {
        if (!in_array($flags, array(FormInterface::VALUES_NORMALIZED, FormInterface::VALUES_RAW))) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects the $flags argument to be one of "%s" or "%s"; received "%s"',
                __METHOD__,
                'Zend\Form\FormInterface::VALUES_NORMALIZED',
                'Zend\Form\FormInterface::VALUES_RAW',
                $flags
            ));
        }

        if ($this->baseFieldset !== null) {
            $this->baseFieldset->setObject($object);
        }

        $this->bindAs = $flags;
        $this->setObject($object);

        $data = $this->extract();
        $this->populateValues($data, true);

        // If a field is flagged, send to IdAware children
        if ($this->getIdField() !== null) {
            $this->setId($data[$this->getIdField()]);
        }

        return $this;
    }

    /**
     * Set the hydrator to use when binding an object to the element
     *
     * @param  HydratorInterface $hydrator
     * @return FieldsetInterface
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        if ($this->baseFieldset !== null) {
            $this->baseFieldset->setHydrator($hydrator);
        }

        return parent::setHydrator($hydrator);
    }

    /**
     * Set the base fieldset to use when hydrating
     *
     * @param  FieldsetInterface $baseFieldset
     * @return self
     * @throws Exception\InvalidArgumentException
     */
    public function setBaseFieldset(FieldsetInterface $baseFieldset)
    {
        $this->baseFieldset = $baseFieldset;
        return $this;
    }

    /**
     * Get the base fieldset to use when hydrating
     *
     * @return FieldsetInterface
     */
    public function getBaseFieldset()
    {
        return $this->baseFieldset;
    }

    /**
     * Are the form elements/fieldsets names wrapped by the form name ?
     *
     * @param  bool $wrapElements
     * @return self
     */
    public function setWrapElements($wrapElements)
    {
        $this->wrapElements = (bool) $wrapElements;
        return $this;
    }

    /**
     * If true, form elements/fieldsets name's are wrapped around the form name itself
     *
     * @return bool
     */
    public function wrapElements()
    {
        return $this->wrapElements;
    }

    /**
     * {@inheritDoc}
     *
     * @param bool $onlyBase
     */
    public function populateValues($data, $onlyBase = false)
    {
        if ($onlyBase && $this->baseFieldset !== null) {
            $name = $this->baseFieldset->getName();
            if (array_key_exists($name, $data)) {
                $this->baseFieldset->populateValues($data[$name]);
            }
        } else {
            parent::populateValues($data);
        }
    }

    /**
     * Recursively extract values for elements and sub-fieldsets
     *
     * @return array
     */
    protected function extract()
    {
        if (null !== $this->baseFieldset) {
            $name = $this->baseFieldset->getName();
            $values[$name] = $this->baseFieldset->extract();
        } else {
            $values = parent::extract();
        }

        return $values;
    }

    /**
     * =======================
     *   IdProviderInterface
     * =======================
     */

    /**
     * A key value used to identify the object with which the row is associated
     *
     * @var bool
     */
    protected $idField = null;


    /**
     * Store the key (and relay to children)
     *
     * @param $id string
     * @return RowInterface
     */
    public function setIdField($id)
    {
        $this->idField = $id;

        return $this;
    }

    /**
     * Get the filed that will provide the key
     *
     * @return string|null
     */
    public function getIdField()
    {
        return $this->idField;
    }

    /**
     * ====================
     *   IdAwareInterface
     * ====================
     */

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
        foreach ($this as $elementOrCollection) {
            if ($elementOrCollection instanceof IdAwareInterface) {
                $elementOrCollection->setId($id);
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

    /**
     * We need to patch the standard getFormFactory to ensure we support custom links
     *
     * @return Factory
     */
    public function getFormFactory()
    {
        /** @var Factory $formFactory */
        return parent::getFormFactory();
    }
}