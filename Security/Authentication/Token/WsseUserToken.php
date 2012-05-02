<?php

namespace Orkestra\Bundle\WebServiceBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken,
    Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Security token for WSSE authenticated requests
 */
class WsseUserToken extends AbstractToken
{
    /**
     * Creates a WsseUserToken from a X-WSSE header value
     *
     * @static
     *
     * @param string $header The X-WSSE header value
     *
     * @return \Orkestra\WebServiceBundle\Security\Authentication\Token\WsseUserToken
     * @throws \Symfony\Component\Security\Core\Exception\AuthenticationException if the given header value is not valid
     */
    public static function createFromHeader($header)
    {
        $wsseRegex = '/UsernameToken Username="([^"]+)", PasswordDigest="([^"]+)", Nonce="([^"]+)", Created="([^"]+)"/';

        if (preg_match($wsseRegex, $header, $matches)) {
            return new WsseUserToken($matches[1], $matches[2], $matches[3], $matches[4]);
        }

        throw new AuthenticationException('Could not create token. The header format is invalid');
    }

    /**
     * @var string
     */
    protected $_digest;

    /**
     * @var string
     */
    protected $_created;

    /**
     * @var string
     */
    protected $_nonce;

    /**
     * Constructor
     *
     * @param string $username
     * @param string $digest
     * @param string $nonce
     * @param string $created
     * @param array $roles An array of roles
     */
    public function __construct($username, $digest, $nonce, $created, array $roles = array())
    {
        parent::__construct($roles);
        $this->setUser($username);
        $this->_digest = $digest;
        $this->_nonce = $nonce;
        $this->_created = $created;
    }

    /**
     * Gets the token's credentials
     *
     * @return string
     */
    public function getCredentials()
    {
        return '';
    }

    /**
     * Gets the created timestamp
     *
     * @return string
     */
    public function getCreated()
    {
        return $this->_created;
    }

    /**
     * Gets the password digest
     *
     * @return string
     */
    public function getDigest()
    {
        return $this->_digest;
    }

    /**
     * Gets the nonce
     *
     * @return string
     */
    public function getNonce()
    {
        return $this->_nonce;
    }
}