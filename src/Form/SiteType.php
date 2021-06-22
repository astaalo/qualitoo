<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SiteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('libelle', null, array('label' => 'Nom du site'));
        $builder->add('responsable', EntityType::class, array(
        		'label' => 'Processus',
        		'class' => Utilisateur::class,
        		'placeholder' => 'Choisir le responsable ...',
        		'empty_data'  => null,
        		'attr' => array(
        				'class' => 'chzn-select',
        		)
        ));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Site'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'site';
    }
}
