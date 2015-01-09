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
use Orkestra\Bundle\WebServiceBundle\Controller\FilterRequestContentInterface;
use Pocomos\Bundle\ApplicationBundle\Http\JsonResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
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

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($event->getRequest()->getRequestFormat() !== 'json') {
            return;
        }

        $exception = $event->getException();
        if ($exception instanceof HttpExceptionInterface) {
            $event->setResponse(new JsonResponse(null, $exception->getStatusCode()));
        }

        $event->setResponse(new JsonResponse(null, 500));
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::EXCEPTION => 'onKernelException'
        );
    }
}
