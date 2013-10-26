<?php

/*
 * This file is part of the OrkestraWebServiceBundle package.
 *
 * Copyright (c) Orkestra Community
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Orkestra\Bundle\WebServiceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\Form\FormError;

use Orkestra\OrkestraBundle\Controller\Controller;

use Orkestra\Bundle\WebServiceBundle\Entity\Token,
    Orkestra\Bundle\WebServiceBundle\Form\TokenType,
    Orkestra\Bundle\WebServiceBundle\Listing\TokenOptions;

/**
 * Token controller.
 *
 * @Route()
 */
class TokenController extends Controller
{
    /**
     * Lists all Token entities.
     *
     * @Route("/tokens", name="orkestra_tokens")
     * @Template()
     */
    public function indexAction()
    {
        $listing = $this->createListing(new TokenOptions($this->getDoctrine()->getEntityManager()));

        return array(
            'listing' => $listing
        );
    }

    /**
     * Finds and displays a Token entity.
     *
     * @Route("/token/{id}/show", name="orkestra_token_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $token = $em->getRepository('Orkestra\Bundle\WebServiceBundle\Entity\Token')->find($id);

        if (!$token) {
            throw $this->createNotFoundException('Unable to locate Token');
        }

        return array(
            'token' => $token,
        );
    }

    /**
     * Displays a form to create a new Token entity.
     *
     * @Route("/token/new", name="orkestra_token_new")
     * @Template()
     */
    public function newAction()
    {
        $token = new Token();
        $form = $this->createForm(new TokenType(), $token);

        return array(
            'token' => $token,
            'form' => $form->createView()
        );
    }

    /**
     * Creates a new Token entity.
     *
     * @Route("/token/create", name="orkestra_token_create")
     * @Method("post")
     * @Template("WebServiceBundle:Token:new.html.twig")
     */
    public function createAction()
    {
        $token = new Token();
        $form = $this->createForm(new TokenType(), $token);
        $form->bindRequest($this->getRequest());

        if ($form->isValid()) {
            $factory = $this->get('security.encoder_factory');
            try {
                $encoder = $factory->getEncoder($token);
                $token->setPassword($encoder->encodePassword($token->getPassword(), $token->getSalt()));
            } catch (\RuntimeException $e) { }

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($token);
            $em->flush();

            $this->get('session')->setFlash('success', 'The token has been created.');
            return $this->redirect($this->generateUrl('orkestra_token_show', array('id' => $token->getId())));
        }

        return array(
            'token' => $token,
            'form' => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Token entity.
     *
     * @Route("/token/{id}/edit", name="orkestra_token_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $token = $em->getRepository('Orkestra\Bundle\WebServiceBundle\Entity\Token')->find($id);

        if (!$token) {
            throw $this->createNotFoundException('Unable to locate Token');
        }

        $form = $this->createForm(new TokenType(false), $token);

        return array(
            'token' => $token,
            'form' => $form->createView(),
        );
    }

    /**
     * Edits an existing Token entity.
     *
     * @Route("/token/{id}/update", name="orkestra_token_update")
     * @Method("post")
     * @Template("WebServiceBundle:Token:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $token = $em->getRepository('Orkestra\Bundle\WebServiceBundle\Entity\Token')->find($id);

        if (!$token) {
            throw $this->createNotFoundException('Unable to locate Token');
        }

        $form = $this->createForm(new TokenType(false), $token);

        $form->bindRequest($this->getRequest());

        if ($form->isValid()) {
            $em->persist($token);
            $em->flush();

            $this->get('session')->setFlash('success', 'Your changes have been saved.');
            return $this->redirect($this->generateUrl('orkestra_token_show', array('id' => $id)));
        }

        return array(
            'token' => $token,
            'form' => $form->createView(),
        );
    }
}
