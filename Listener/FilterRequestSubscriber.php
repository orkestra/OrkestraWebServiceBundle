<?php

/*
 * This file is part of the OrkestraWebServiceBundle package.
 *
 * Copyright (c) Orkestra Community
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Orkestra\Bundle\WebServiceBundle\Listener;

use Negotiation\FormatNegotiator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class FilterRequestSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Negotiation\FormatNegotiator
     */
    private $formatNegotiator;

    /**
     * Constructor
     *
     * @param FormatNegotiator $formatNegotiator
     */
    public function __construct(FormatNegotiator $formatNegotiator)
    {
        $this->formatNegotiator = $formatNegotiator;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        $request->setRequestFormat($this->formatNegotiator->getBestFormat($request->headers->get('accept')));
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller) || !is_subclass_of($controller[0], 'Orkestra\Bundle\WebServiceBundle\Controller\FilterRequestContentInterface'))
            return;

        $request = $event->getRequest();
        $content = $request->getContent();

        switch ($request->getRequestFormat()) {
            case 'json':
                $controller[0]->setRequestContent(json_decode($content));
                break;
            case 'xml':
                $controller[0]->setRequestContent(simplexml_load_string($content));
                break;
            default:
                $controller[0]->setRequestContent($content);
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::CONTROLLER => 'onKernelController'
        );
    }
}
