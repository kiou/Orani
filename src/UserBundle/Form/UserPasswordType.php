<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserPasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('username')
            ->remove('nom')
            ->remove('prenom')
            ->remove('email')
            ->remove('roles')
            ->remove('file')
        ;
    }
    
    public function getParent()
    {
        return UserType::class;
    }
}
