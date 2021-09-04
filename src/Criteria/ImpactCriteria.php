<?php
namespace App\Criteria;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImpactCriteria extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        	->add('menace', EntityType::class, array('class'=>'App\Entity\Menace', 'placeholder'=>'Chosir un risque ...', 'attr'=>array('class'=>'chzn-select')))
        	//->add('profilRisque', EntityType::class, array('class'=>'App\Entity\ProfilRisque', 'placeholder'=>'Chosir un profil risque ...'))
        	->add('domaine', EntityType::class, array('class'=>'App\Entity\DomaineImpact', 'placeholder'=>'Chosir un domaine ...'));
    }
	
	public function configureOptions(OptionsResolver $resolver)
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
