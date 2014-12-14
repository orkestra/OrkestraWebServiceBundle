<?php

/*
 * This file is part of the OrkestraApplicationBundle package.
 *
 * Copyright (c) Orkestra Community
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Orkestra\Bundle\WebServiceBundle\Model;

/**
 * Defines the contract any user group must implement
 */
interface GroupInterface extends PersistentModelInterface
{
    /**
     * Set the name of this Group
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get the name of this Group
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the role this Group provides
     *
     * @param string $role
     */
    public function setRole($role);

    /**
     * Gets the role this Group provides
     *
     * @see RoleInterface
     */
    public function getRole();

    /**
     * Add a user to this Group
     *
     * @param UserInterface $user
     */
    public function addUser(UserInterface $user);

    /**
     * Get all users in this Group
     *
     * @return \Doctrine\Common\Collections\Collection|UserInterface[]
     */
    public function getUsers();
}
