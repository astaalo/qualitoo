<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\DataTransformer\EntityToIdTransformer;

class ReponseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add($builder->create('question', HiddenType::class)->addModelTransformer(new EntityToIdTransformer($options['attr']['em'], '\App\Entity\Question')))
            ->add('valide', null, array('attr' => array('class' => 'on_off_checkbox')));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Reponse'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reponse';
    }
}
