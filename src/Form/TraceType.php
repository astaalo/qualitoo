<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Translatable\Fixture\Document\Personal\ArticleTranslation;
use Doctrine\ORM\EntityRepository;

class TraceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', null, array('label' => 'Description'))
            ->add('typeTrace', null, array('label' =>'TypeTrace', 'empty_value' => 'choisir Type trace'
            		,'attr' => array('class' => 'chzn-select')))
            ->add('utilisateur', null, array('label' =>'Utilisateur', 'empty_value' => 'choisir utilisateur '
            		,'attr' => array('class' => 'chzn-select')))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Trace'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'trace';
    }
}
