<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', 'text', array('label'=>'Description du test: '))
            ->add('risques', 'collection', array(
            		'type' => new AuditHasRisqueType(), 'allow_add'=>true, 'by_reference' => false,
            		'label' => 'Risques:', 'attr' => array('label_help' =>'Risque')
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Audit',
        		'validation_groups' =>  array('audit')
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orange_mainbundle_audit';
    }
}
