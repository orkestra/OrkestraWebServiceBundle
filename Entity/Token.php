<?php

/*
 * This file is part of the OrkestraWebServiceBundle package.
 *
 * Copyright (c) Orkestra Community
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Orkestra\Bundle\WebServiceBundle\Entity;

use Symfony\Component\Security\Core\User\AdvancedUserInterface,
    Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

use Orkestra\Common\Entity\AbstractEntity;

/**
 * Defines a user that is able to interact with web services
 *
 * @ORM\Table(name="orkestra_tokens")
 * @ORM\Entity(repositoryClass="Orkestra\Bundle\WebServiceBundle\Entity\Repository\TokenRepository")
 */
class Token extends AbstractEntity implements AdvancedUserInterface
{
    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=60, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=40)
     */
    private $salt;

    /**
     * @ORM\ManyToMany(targetEntity="Orkestra\Bundle\WebServiceBundle\Model\GroupInterface", fetch="EAGER")
     * @ORM\JoinTable(name="orkestra_tokens_groups",
     *     joinColumns={@ORM\JoinColumn(name="token_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    private $groups;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->salt   = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->username;
    }

    /**
     * Sets the username
     *
     * @param $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Sets the password
     *
     * @param $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Adds the user to a group
     *
     * @param \Orkestra\Bundle\WebServiceBundle\Model\GroupInterface $group
     */
    public function addGroup($group)
    {
        $this->groups->add($group);
    }

    /**
     * Gets the groups this user is assigned to
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    #region AdvancedUserInterface members

    /**
     * Gets the roles available to the user
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->groups->toArray();
    }

    /**
     * Compares this user with another to determine sameness
     *
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     *
     * @return bool
     */
    public function equals(UserInterface $user)
    {
        return $user->getUsername() === $this->username;
    }

    /**
     * Erases the user's credentials
     */
    public function eraseCredentials()
    {
    }

    /**
     * Gets the username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Gets the salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Gets the password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns true if the user is not expired
     *
     * @return bool
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * Returns true if the user is not locked
     *
     * @return bool
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * Returns true if the user's credentials are not expired
     *
     * @return bool
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * Returns true if the user is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->active;
    }
}
