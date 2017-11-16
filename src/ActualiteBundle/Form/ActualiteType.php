<?php

namespace ActualiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ActualiteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class)
            ->add('slug', TextType::class)
            ->add('debut', DateType::class, array(
                    'widget' => 'single_text',
                    'html5' => false,
                    'format' => 'dd/MM/yyyy'
                )
            )
            ->add('fileimage', FileType::class)
            ->add('resume', TextareaType::class)
            ->add('contenu', TextareaType::class)
            ->add('Enregistrer', SubmitType::class, array(
                    'attr' => array('class' => 'form-submit turquoise medium')
                )
            );
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ActualiteBundle\Entity\Actualite'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'actualitebundle_actualite';
    }


}
