<?php

namespace Orkestra\Bundle\WebServiceBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

/**
 * Form type for Tokens
 */
class TokenType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilder $builder
     * @param array $options
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('username')
            ->add('password')
            ->add('active', null, array('required' => false))
            ->add('groups', null, array('required' => false));
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Orkestra\Bundle\WebServiceBundle\Entity\Token',
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orkestra_token';
    }
}
