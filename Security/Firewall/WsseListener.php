<?php

namespace Orkestra\WebServiceBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpKernel\Event\GetResponseEvent,
    Symfony\Component\Security\Http\Firewall\ListenerInterface,
    Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Component\Security\Core\SecurityContextInterface,
    Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Orkestra\WebServiceBundle\Security\Authentication\Token\WsseUserToken;

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
            } catch (AuthenticationException $e) { }
        }

        $response = new Response();
        $response->setStatusCode(403);
        $event->setResponse($response);
    }
}