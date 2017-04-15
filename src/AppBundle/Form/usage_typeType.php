<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class usage_typeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'required' => true,
                'attr' => array(
                    'style' => 'width: 200px',
                    'maxlength' => '100',
                 )))
            ->add('precedence', IntegerType::class, array(
                'required' => true,
                'attr' => array(
                    'style' => 'width: 200px',
                    'maxlength' => '2',
                    'min' => '1',
                    'step' => '1'
                )))
            ->add('start', 'time')
            ->add('end', 'time')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\usage_type'
        ));
    }
}
