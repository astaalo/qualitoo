<?php
namespace App\Criteria;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjetCriteria extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder
    		->add('processus')
    		->add('utilisateur');
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
    	$resolver->setDefaults(array(
				'data_class' => 'App\Entity\Projet',
				'csrf_protection' => false
        	));
    }
    
    public function getName() {
    	return 'projet_criteria';
    }
}