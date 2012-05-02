<?php

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
        $contentType = $request->headers->get('content-type', 'application/json');
        $content = $request->getContent();

        switch ($contentType) {
            case 'application/json':
                $controller[0]->setRequestContent(json_decode($content));
                break;
            case 'application/xml':
                $controller[0]->setRequestContent(simplexml_load_string($content));
                break;
            default:
                $controller[0]->setRequestContent($content);
        }
    }
}