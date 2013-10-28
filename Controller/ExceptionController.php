<?php

namespace Orkestra\Bundle\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController as BaseController;
use Symfony\Component\HttpKernel\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExceptionController extends BaseController
{
    /**
     * Converts an Exception to a Response.
     *
     * @param Request              $request   The request
     * @param FlattenException     $exception A FlattenException instance
     * @param DebugLoggerInterface $logger    A DebugLoggerInterface instance
     * @param string               $_format   The format to use for rendering (html, xml, ...)
     *
     * @return Response
     *
     * @throws \InvalidArgumentException When the exception template does not exist
     */
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null, $_format = 'html')
    {
        return parent::showAction($request, $exception, $logger, $request->getRequestFormat());
    }
}
