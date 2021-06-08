<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', null, array('label' => 'Libellé', 'attr' => array('class' => 'full')))
            ->add('cotation', null, array('attr' => array('class' => 'xsmall')))
            ->add('etat', null, array('label' => 'Actif','attr' => array('class' => 'on_off_checkbox')));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Question'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'question';
    }
}
