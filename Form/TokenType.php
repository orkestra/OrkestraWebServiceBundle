<?php

/*
 * This file is part of the OrkestraWebServiceBundle package.
 *
 * Copyright (c) Orkestra Community
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Orkestra\Bundle\WebServiceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form type for Tokens
 */
class TokenType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilder $builder
     * @param array                               $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username')
            ->add('password')
            ->add('active', null, array('required' => false))
            ->add('groups', null, array('required' => false));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orkestra\Bundle\WebServiceBundle\Entity\Token',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orkestra_token';
    }
}
