<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizType extends AbstractType
{	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	
        $builder->add('reponse', 'collection', array('type' => new ReponseType(), 'by_reference' => false, 'options' => array('attr'=>array('em'=>$options['attr']['em']))));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Quiz'
        ));
    }
	
    /**
     * @return string
     */
    public function getName()
    {
        return 'quiz';
    }
}
