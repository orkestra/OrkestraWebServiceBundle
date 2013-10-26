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

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

use Orkestra\Bundle\WebServiceBundle\Controller\FilterRequestContentInterface;

class FilterRequestListener
{
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller) || !is_subclass_of($controller[0], 'Orkestra\Bundle\WebServiceBundle\Controller\FilterRequestContentInterface'))
            return;

        $request = $event->getRequest();
        $content = $request->getContent();
        $contentType = $request->get('content-type', 'application/json');
        $format = $request->getFormat($contentType);

        switch ($format) {
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
}