<?php
/**
 * Created by PhpStorm.
 * User: Clayton Daley
 * Date: 2/20/2015
 * Time: 1:50 PM
 */

namespace DaleyTable\Delegator;


use Zend\Form\View\Helper\FormElement;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FormElementLinkSupport implements DelegatorFactoryInterface
{

    /**
     * A factory that creates delegates of a given service
     *
     * @param ServiceLocatorInterface $serviceLocator the service locator which requested the service
     * @param string $name the normalized service name
     * @param string $requestedName the requested service name
     * @param callable $callback the callback that is responsible for creating the service
     *
     * @return mixed
     */
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        /** @var $formElement FormElement */
        $formElement = $callback();
        $formElement->addType('link', 'formlink');
        $formElement->addClass('DaleyTable\Element\Link', 'formlink');
        return $formElement;
    }
}