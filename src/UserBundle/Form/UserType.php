<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class)
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('email', EmailType::class)
            ->add('roles', ChoiceType::class, array(
                    'multiple' => true,
                    'label'=>'Choisir un rôle',
                    'expanded' => true,
                    'choices' => array(
                        'ROLE_ADMIN' => 'ROLE_ADMIN',
                        'ROLE_USER' => 'ROLE_USER'
                    )
                )
            )
            ->add('file', FileType::class)
            ->add('password', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'invalid_message' => 'user.validators.passwordconfirm',
                    'options' => array('attr' => array('class' => 'form-elem big')),
                    'first_options' => array('label' => 'user.label.password'),
                    'second_options' => array('label' => 'user.label.passwordconfirmation')
                )
            )
            ->add('Enregistrer', SubmitType::class, array(
                    'attr' => array('class' => 'form-submit turquoise medium')
                )
            );
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User'
        ));
    }
}
