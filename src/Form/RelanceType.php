<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RelanceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nbDebut')
            ->add('nbFrequence')
            ->add('phase',null,array('label' => 'A partir de la phase','empty_value' => 'Choisir', 'attr' => array('class' => 'chzn-select')))
            ->add('dateCreation')
            ->add('isActif', null, array('label' => 'Activer les relances', 'required' => true,'attr' => array('class' => 'on_off_checkbox')))
            ->add('societe')
            ->add('uniteTpsDebut',  null, array('empty_value' => 'Choisir ', 'attr' => array('class' => 'chzn-select')))
            ->add('uniteTpsFrequence',  null, array('empty_value' => 'Choisir', 'attr' => array('class' => 'chzn-select')))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Relance'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orange_mainbundle_relance';
    }
}
