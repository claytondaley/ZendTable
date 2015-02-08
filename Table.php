<?php
/**
 * Created by PhpStorm.
 * User: Clayton Daley
 * Date: 2/7/2015
 * Time: 12:06 PM
 */

namespace DaleyTable;


use ArrayIterator;
use IteratorAggregate;
use Traversable;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;
use Zend\Form\Factory;
use Zend\Form\FieldsetInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

class Table
    implements TableInterface, IdProviderInterface
{

    /** @var RowInterface $row_prototype */
    protected $row_prototype = null;

    /** @var IteratorAggregate|null $objects */
    protected $objects = null;

    /**
     * @param RowInterface $row_prototype
     */
    public function __construct(RowInterface $row_prototype = null) {
        if ($row_prototype !== null) {
            $this->setRowPrototype($row_prototype);
        }

        $this->getEventManager()->trigger('init', $this);
    }

    public function init(){}

    /**
     * @param RowInterface|null $row_prototype
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setRowPrototype(RowInterface $row_prototype)
    {
        if (!$row_prototype instanceof RowInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a RowInterface argument; received "%s"',
                __METHOD__,
                (is_object($row_prototype) ? get_class($row_prototype) : gettype($row_prototype))
            ));
        }
        $this->row_prototype = $row_prototype;

        return $this;
    }

    /**
     * @return RowInterface
     * @throws Exception\InvalidArgumentException
     */
    public function getRowPrototype()
    {
        if (!$this->row_prototype instanceof RowInterface) {
            $this->setRowPrototype(new StaticRow());
        }

        return $this->row_prototype;
    }

    /**
     * =============================================
     *    PROXY RowInterface methods to prototype
     * =============================================
     */

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        if ($this->objects !== null) {
            $iterator = $this->objects->getIterator();
        } else {
            $iterator = new ArrayIterator(array());
        }

        return new RowIterator($this->row_prototype, $iterator);
    }

    /**
     * Set the name of this element
     *
     * In most cases, this will proxy to the attributes for storage, but is
     * present to indicate that elements are generally named.
     *
     * @param  string $name
     * @return ElementInterface
     */
    public function setName($name)
    {
        $this->getRowPrototype()->setName($name);

        return $this;
    }

    /**
     * Retrieve the element name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getRowPrototype()->getName();
    }

    /**
     * Set options for an element
     *
     * @param  array|\Traversable $options
     * @return ElementInterface
     */
    public function setOptions($options)
    {
        $this->getRowPrototype()->setOptions($options);

        return $this;
    }

    /**
     * Set a single option for an element
     *
     * @param  string $key
     * @param  mixed $value
     * @return self
     */
    public function setOption($key, $value)
    {
        $this->getRowPrototype()->setOption($key, $value);

        return $this;
    }

    /**
     * get the defined options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->getRowPrototype()->getOptions();
    }

    /**
     * return the specified option
     *
     * @param string $option
     * @return null|mixed
     */
    public function getOption($option)
    {
        return $this->getRowPrototype()->getOptions($option);
    }

    /**
     * Set a single element attribute
     *
     * @param  string $key
     * @param  mixed $value
     * @return ElementInterface
     */
    public function setAttribute($key, $value)
    {
        $this->getRowPrototype()->setAttribute($key, $value);

        return $this;
    }

    /**
     * Retrieve a single element attribute
     *
     * @param  string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        return $this->getRowPrototype()->getAttribute($key);
    }

    /**
     * Return true if a specific attribute is set
     *
     * @param  string $key
     * @return bool
     */
    public function hasAttribute($key)
    {
        return $this->getRowPrototype()->hasAttribute($key);
    }

    /**
     * Set many attributes at once
     *
     * Implementation will decide if this will overwrite or merge.
     *
     * @param  array|\Traversable $arrayOrTraversable
     * @return ElementInterface
     */
    public function setAttributes($arrayOrTraversable)
    {
        $this->getRowPrototype()->setAttributes($arrayOrTraversable);

        return $this;
    }

    /**
     * Retrieve all attributes at once
     *
     * @return array|\Traversable
     */
    public function getAttributes()
    {
        return $this->getRowPrototype()->getAttributes();
    }

    /**
     * Set the value of the element
     *
     * @param  mixed $value
     * @return ElementInterface
     */
    public function setValue($value)
    {
        $this->getRowPrototype()->setValue($value);

        return $this;
    }

    /**
     * Retrieve the element value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->getRowPrototype()->getValue();
    }

    /**
     * Set the label (if any) used for this element
     *
     * @param  $label
     * @return ElementInterface
     */
    public function setLabel($label)
    {
        $this->getRowPrototype()->setLabel($label);

        return $this;
    }

    /**
     * Retrieve the label (if any) used for this element
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->getRowPrototype()->getLabel();
    }

    /**
     * Set a list of messages to report when validation fails
     *
     * @param  array|\Traversable $messages
     * @return ElementInterface
     */
    public function setMessages($messages)
    {
        $this->getRowPrototype()->setMessages($messages);

        return $this;
    }

    /**
     * Get validation error messages, if any
     *
     * Returns a list of validation failure messages, if any.
     *
     * @return array|\Traversable
     */
    public function getMessages()
    {
        return $this->getRowPrototype()->getMessages();
    }

    /**
     * Prepare the form element (mostly used for rendering purposes)
     *
     * @param FieldsetInterface $table
     * @return mixed
     */
    public function prepareElement(FieldsetInterface $table)
    {
        return $this->getRowPrototype()->prepareElement($table);
    }

    /**
     * Add an element or fieldset
     *
     * $flags could contain metadata such as the alias under which to register
     * the element or fieldset, order in which to prioritize it, etc.
     *
     * @param  array|\Traversable|ElementInterface $elementOrFieldset Typically, only allow objects implementing ElementInterface;
     *                                                                however, keeping it flexible to allow a factory-based form
     *                                                                implementation as well
     * @param  array $flags
     * @return FieldsetInterface
     */
    public function add($elementOrFieldset, array $flags = array())
    {
        $this->getRowPrototype()->add($elementOrFieldset, $flags);

        return $this;
    }

    /**
     * Does the fieldset have an element/fieldset by the given name?
     *
     * @param  string $elementOrFieldset
     * @return bool
     */
    public function has($elementOrFieldset)
    {
        return $this->getRowPrototype()->has($elementOrFieldset);
    }

    /**
     * Retrieve a named element or fieldset
     *
     * @param  string $elementOrFieldset
     * @return ElementInterface
     */
    public function get($elementOrFieldset)
    {
        return $this->getRowPrototype()->get($elementOrFieldset);
    }

    /**
     * Remove a named element or fieldset
     *
     * @param  string $elementOrFieldset
     * @return FieldsetInterface
     */
    public function remove($elementOrFieldset)
    {
        $this->getRowPrototype()->remove($elementOrFieldset);

        return $this;
    }

    /**
     * Set/change the priority of an element or fieldset
     *
     * @param string $elementOrFieldset
     * @param int $priority
     * @return FieldsetInterface
     */
    public function setPriority($elementOrFieldset, $priority)
    {
        $this->getRowPrototype()->setPriority($elementOrFieldset, $priority);

        return $this;
    }

    /**
     * Retrieve all attached elements
     *
     * Storage is an implementation detail of the concrete class.
     *
     * @return array|\Traversable
     */
    public function getElements()
    {
        return $this->getRowPrototype()->getElements();
    }

    /**
     * Retrieve all attached fieldsets
     *
     * Storage is an implementation detail of the concrete class.
     *
     * @return array|\Traversable
     */
    public function getFieldsets()
    {
        return $this->getRowPrototype()->getFieldsets();
    }

    /**
     * Recursively populate value attributes of elements
     *
     * @param  array|\Traversable $data
     * @return void
     */
    public function populateValues($data)
    {
        $this->getRowPrototype()->populateValues($data);
    }

    /**
     * Set the object used by the hydrator
     *
     * @param  $object
     * @return FieldsetInterface
     */
    public function setObject($object)
    {
        $this->getRowPrototype()->setObject($object);

        return $this;
    }

    /**
     * Get the object used by the hydrator
     *
     * @return mixed
     */
    public function getObject()
    {
        return $this->getRowPrototype()->getObject();
    }

    /**
     * Checks if the object can be set in this fieldset
     *
     * @param $object
     * @return bool
     */
    public function allowObjectBinding($object)
    {
        return $this->getRowPrototype()->allowObjectBinding($object);
    }

    /**
     * Set the hydrator to use when binding an object to the element
     *
     * @param  HydratorInterface $hydrator
     * @return FieldsetInterface
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->getRowPrototype()->setHydrator($hydrator);

        return $this;
    }

    /**
     * Get the hydrator used when binding an object to the element
     *
     * @return null|HydratorInterface
     */
    public function getHydrator()
    {
        return $this->getRowPrototype()->getHydrator();
    }

    /**
     * Bind values to the bound object
     *
     * @param  array $values
     * @return mixed
     */
    public function bindValues(array $values = array())
    {
        return $this->getRowPrototype()->bindValues($values);
    }

    /**
     * Checks if this fieldset can bind data
     *
     * @return bool
     */
    public function allowValueBinding()
    {
        return false;
    }

    /**
     * Compose a form factory into the object
     *
     * @param Factory $factory
     * @return $this
     */
    public function setFormFactory(Factory $factory)
    {
        $this->getRowPrototype()->setFormFactory($factory);

        return $this;
    }

    public function getFormFactory()
    {
        /** @var Factory $formFactory */
        return $this->getRowPrototype()->getFormFactory();
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return $this->getRowPrototype()->count();
    }

    /**
     * Bind a traversable of object to the element
     *
     * Allows populating the object with validated values.
     *
     * @param  traversable $objects
     * @return mixed
     */
    public function bind($objects)
    {
        $this->objects = $objects;
        $this->iterator = null;

        return $this;
    }

    /**
     * =====================================
     *   PROXY IdProviderInterface Methods
     * =====================================
     */

    /**
     * Set the field that will provide the key
     *
     * @param $id string
     * @return RowInterface
     */
    public function setIdField($id)
    {
        $this->getRowPrototype()->setIdField($id);

        return $this;
    }

    /**
     * Get the filed that will provide the key
     *
     * @return string|null
     */
    public function getIdField()
    {
        return $this->getRowPrototype()->getIdField();
    }

    /**
     * =======================================
     *   Support Events to support Extension
     * =======================================
     */

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * Set the event manager instance used by this context
     *
     * @param  EventManagerInterface $events
     * @return mixed
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $this->events = $events;
        return $this;
    }

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if (!$this->events instanceof EventManagerInterface) {
            $identifiers = array(__CLASS__, get_called_class());
            if (isset($this->eventIdentifier)) {
                if ((is_string($this->eventIdentifier))
                    || (is_array($this->eventIdentifier))
                    || ($this->eventIdentifier instanceof Traversable))
                {
                    $identifiers = array_unique($identifiers + (array) $this->eventIdentifier);
                } elseif (is_object($this->eventIdentifier)) {
                    $identifiers[] = $this->eventIdentifier;
                }
                // silently ignore invalid eventIdentifier types
            }
            $this->setEventManager(new EventManager($identifiers));
        }
        return $this->events;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceLocatorInterface $serviceManager
     * @return $this
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceManager)
    {
        $this->serviceLocator = $serviceManager;
        return $this;
    }
}