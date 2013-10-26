<?php

/*
 * This file is part of the OrkestraWebServiceBundle package.
 *
 * Copyright (c) Orkestra Community
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Orkestra\Bundle\WebServiceBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface,
    Symfony\Component\Security\Core\User\UserProviderInterface,
    Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Component\Security\Core\Exception\NonceExpiredException,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Orkestra\Bundle\WebServiceBundle\Security\Authentication\Token\WsseUserToken;

/**
 * WSSE Authentication Provider
 *
 * Allows clients to use WSSE authentication
 */
class WsseProvider implements AuthenticationProviderInterface
{
    /**
     * @var \Symfony\Component\Security\Core\User\UserProviderInterface
     */
    private $_userProvider;

    /**
     * @var string
     */
    private $_cacheDir;

    /**
     * @var int The time, in seconds, that a nonce is considered valid
     */
    private $_nonceLifetime;

    /**
     * @param \Symfony\Component\Security\Core\User\UserProviderInterface $userProvider
     * @param string                                                      $cacheDir
     * @param int                                                         $lifetime
     */
    public function __construct(UserProviderInterface $userProvider, $cacheDir, $lifetime = 300)
    {
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        $this->_userProvider = $userProvider;
        $this->_cacheDir = $cacheDir;
        $this->_nonceLifetime = $lifetime;
    }

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     *
     * @return \Orkestra\WebServiceBundle\Security\Authentication\Token\WsseUserToken
     * @throws \Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function authenticate(TokenInterface $token)
    {
        $user = $this->_userProvider->loadUserByUsername($token->getUsername());

        if ($user && $this->_validateDigest($token->getDigest(), $token->getNonce(), $token->getCreated(), $user->getPassword())) {
            return new WsseUserToken($user, '', '', '', $user->getRoles());
        }

        throw new AuthenticationException('WSSE authentication failed');
    }

    /**
     * Validates the digest passed by the client
     *
     * @param string $digest
     * @param string $nonce
     * @param string $created
     * @param string $secret
     *
     * @return bool
     * @throws \Symfony\Component\Security\Core\Exception\NonceExpiredException
     */
    protected function _validateDigest($digest, $nonce, $created, $secret)
    {
        if (time() - strtotime($created) > $this->_nonceLifetime) {
            return false;
        }

        if (file_exists($this->_cacheDir . '/' . $nonce) && file_get_contents($this->_cacheDir . '/' . $nonce) + $this->_nonceLifetime < time()) {
            throw new NonceExpiredException('Previously used nonce detected');
        }

        file_put_contents($this->_cacheDir.'/'.$nonce, time());

        $expected = base64_encode(sha1(base64_decode($nonce) . $created . $secret, true));

        return $digest === $expected;
    }

    /**
     * Checks whether this provider supports the given token.
     *
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token A TokenInterface instance
     *
     * @return boolean true if the implementation supports the Token, false otherwise
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof WsseUserToken;
    }
}
