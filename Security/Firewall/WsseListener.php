<?php

/*
 * This file is part of the OrkestraWebServiceBundle package.
 *
 * Copyright (c) Orkestra Community
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Orkestra\Bundle\WebServiceBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpKernel\Event\GetResponseEvent,
    Symfony\Component\Security\Http\Firewall\ListenerInterface,
    Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Component\Security\Core\SecurityContextInterface,
    Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Orkestra\Bundle\WebServiceBundle\Security\Authentication\Token\WsseUserToken;

/**
 * Firewall listener for WSSE Authentication
 */
class WsseListener implements ListenerInterface
{
    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected $_securityContext;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface
     */
    protected $_authenticationManager;

    /**
     * Constructor
     *
     * @param \Symfony\Component\Security\Core\SecurityContextInterface $securityContext
     * @param \Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface $authenticationManager
     */
    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager)
    {
        $this->_securityContext = $securityContext;
        $this->_authenticationManager = $authenticationManager;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $failure = null;

        if ($request->headers->has('x-wsse')) {
            try {
                $token = WsseUserToken::createFromHeader($request->headers->get('x-wsse'));

                $result = $this->_authenticationManager->authenticate($token);

                if ($result instanceof TokenInterface) {
                    return $this->_securityContext->setToken($result);
                }
                else if ($result instanceof Response) {
                    return $event->setResponse($result);
                }
            } catch (AuthenticationException $failure) { }
        }

        $this->_onFailedToAuthenticate($event, $failure);
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     * @param null|\Symfony\Component\Security\Core\Exception\AuthenticationException $failure
     */
    protected function _onFailedToAuthenticate(GetResponseEvent $event, AuthenticationException $failure = null)
    {
        $request = $event->getRequest();
        $format = $request->getRequestFormat('json');

        $error = $failure ? $failure->getMessage() : 'Unable to authenticate';

        switch ($format) {
            case 'xml':
                $contentType = 'text/xml';
                $content = <<<END
<?xml version="1.0" encoding="utf-8" ?>
<response>
    <success>false</success>
    <error>{$error}</error>
</response>
END;
                break;
            case 'json':
            default:
                $contentType = 'application/json';
                $content = json_encode(array('success' => false, 'error' => $error));
        }

        $event->setResponse(new Response($content, 403, array('content-type' => $contentType)));
    }
}