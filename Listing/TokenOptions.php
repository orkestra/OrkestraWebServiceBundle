<?php

namespace Orkestra\Bundle\WebServiceBundle\Listing;

use Orkestra\OrkestraBundle\Component\Listing\ListingOptions,
    Orkestra\OrkestraBundle\Component\Listing\Column,
    Orkestra\OrkestraBundle\Component\Listing\Route,
    Orkestra\OrkestraBundle\Component\Listing\Adapter\DoctrineAdapter;

use Doctrine\ORM\EntityManager;

/**
 * Defines the default listing options for Tokens
 */
class TokenOptions extends ListingOptions
{
    /**
     * Constructor
     *
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $adapter = new DoctrineAdapter($em->createQuery('SELECT t FROM Orkestra\Bundle\WebServiceBundle\Entity\Token t'));

        $this->setAdapter($adapter)
            ->addDisplayColumn(new Column\PropertyColumn('id', 'ID', array('width' => '75px', 'class' => 'center', 'sort_field' => 't.id')))
            ->addDisplayColumn(new Column\PropertyColumn('username', 'Username', array('sort_field' => 't.username')))
            ->addDisplayColumn(new Column\BooleanColumn('active', 'Active', array('width' => '125px', 'class' => 'center', 'sort_field' => 't.active')))
            ->addDisplayColumn(new Column\PropertyColumn('dateModified', 'Date Modified', array('width' => '125px', 'class' => 'center', 'sort_field' => 't.dateModified')))
            ->addDisplayColumn(new Column\PropertyColumn('dateCreated', 'Date Created', array('width' => '125px', 'class' => 'center', 'sort_field' => 't.dateCreated')))
            ->setAddRoute(new Route('orkestra_token_new'))
            ->setEditRoute(new Route('orkestra_token_edit', array('id' => 'id')))
            ->setDefaultAction(ListingOptions::EditDefaultAction);
    }
}