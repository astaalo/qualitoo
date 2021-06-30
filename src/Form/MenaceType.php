<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenaceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('libelle', null, array('label' => 'Libellé'))
        		->add('cartographie',  null, array('label' => 'Traitement ', 'attr' => array('class' => 'chzn-select', 'placeholder' => 'Choisir le traitement')))
           		->add('description');
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Menace'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'menace';
    }
}
