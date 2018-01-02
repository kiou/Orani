<?php

namespace PageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use GlobalBundle\Form\Type\LangueType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use ReferencementBundle\Form\ReferencementType;

class PageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('titre', TextType::class)
                ->add('slug', TextType::class)
                ->add('contenu', TextareaType::class)
                ->add('referencement', ReferencementType::class)
                ->add('langue', LangueType::class)
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
            'data_class' => 'PageBundle\Entity\Page'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'pagebundle_page';
    }


}
