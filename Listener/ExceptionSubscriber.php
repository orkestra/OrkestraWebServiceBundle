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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var bool
     */
    private $debug;

    /**
     * Constructor
     *
     * @param bool $debug
     */
    public function __construct($debug = false)
    {
        $this->debug = $debug;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($event->getRequest()->getRequestFormat() !== 'json') {
            return;
        }

        $code = 500;
        $exception = $event->getException();
        $data = array(
            'code' => $code,
            'message' => $exception->getMessage() ?: 'An internal server error occurred.'
        );
        if ($this->debug) {
            $data['trace'] = $this->getCleanTrace($exception);
        }

        if ($exception instanceof HttpExceptionInterface) {
            $data['code'] = $code = $exception->getStatusCode();
        }

        $event->setResponse(new JsonResponse($data, $code));
    }

    private function getCleanTrace(\Exception $exception)
    {
        $trace = $exception->getTrace();
        return array_map(function($values) {
            if (isset($values['object'])) {
                $values['object'] = get_class($values['object']).':'.spl_object_hash($values['object']);
            }

            if (isset($values['args'])) {
                $args = $values['args'];
                array_walk_recursive($args, function(&$value) {
                    if (is_object($value)) {
                        $value = get_class($value).':'.spl_object_hash($value);
                    }
                });
                $values['args'] = $args;
            }

            return $values;
        }, $trace);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => 'onKernelException'
        );
    }
}
