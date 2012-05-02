<?php

namespace Orkestra\Bundle\WebServiceBundle\Entity;

use Symfony\Component\Security\Core\User\AdvancedUserInterface,
    Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

use Orkestra\Common\Entity\EntityBase,
    Orkestra\OrkestraBundle\Entity\Group;

/**
 * Defines a user that is able to interact with web services
 *
 * @ORM\Table(name="orkestra_tokens")
 * @ORM\Entity(repositoryClass="Orkestra\Bundle\WebServiceBundle\Entity\Repository\TokenRepository")
 */
class Token extends EntityBase implements AdvancedUserInterface
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
     * @ORM\ManyToMany(targetEntity="Orkestra\OrkestraBundle\Entity\Group", fetch="EAGER")
     * @ORM\JoinTable(name="orkestra_token_groups",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
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
        $this->password = sha1(uniqid(mt_rand(), true));
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getUsername();
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
     * @param \Orkestra\OrkestraBundle\Entity\Group $group
     */
    public function addGroup(Group $group)
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
        return '';
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

    #endregion
}