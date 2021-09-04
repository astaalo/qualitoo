<?php
namespace App\Criteria;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RisqueHasImpactCriteria extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('grille', new GrilleCriteria());
        $builder->add('impact', new ImpactCriteria());
        $builder->add('risque', new RisqueCriteria());
    }
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'App\Entity\RisqueHasImpact',
				'csrf_protection' => false
			));
	}

    public function getName()
    {
        return 'risquehasimpact_criteria';
    }
}
