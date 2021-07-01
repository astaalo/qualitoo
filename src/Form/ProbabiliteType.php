<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Translatable\Fixture\Document\Personal\ArticleTranslation;
use Doctrine\ORM\EntityRepository;

class ProbabiliteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', null, array('label' => 'Libellé  '))
            ->add('trace', null, array('label' => 'Trace ', 'empty_value' => 'Choisir la trace'
            		,'attr' => array('class' => 'chzn-select')))
            ->add('entite', null, array('label' => 'Entite ', 'empty_value' => 'Choisir l\'entite'
            		,'attr' => array('class' => 'chzn-select')))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Probabilite'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'probabilite';
    }
}
