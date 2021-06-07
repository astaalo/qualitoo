<?php
namespace App\Criteria;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImpactCriteria extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        	->add('menace', 'entity', array('class'=>'\App\Entity\Menace', 'empty_value'=>'Chosir un risque ...', 'attr'=>array('class'=>'chzn-select')))
        	->add('profilRisque', 'entity', array('class'=>'\App\Entity\ProfilRisque', 'empty_value'=>'Chosir un profil risque ...'))
        	->add('domaine', 'entity', array('class'=>'\App\Entity\DomaineImpact', 'empty_value'=>'Chosir un domaine ...'));
    }
	
	public function setDefaultOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'App\Entity\Impact',
				'csrf_protection' => false
			));
	}

    public function getName()
    {
        return 'impact_criteria';
    }
}
