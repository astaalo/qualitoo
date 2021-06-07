<?php
namespace App\Criteria;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RisqueHasCauseCriteria extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('grille', new GrilleCriteria());
        $builder->add('cause', new CauseCriteria());
        $builder->add('risque', new RisqueCriteria());
    }
	
	public function setDefaultOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'App\Entity\RisqueHasCause',
				'csrf_protection' => false
			));
	}

    public function getName()
    {
        return 'risquehascause_criteria';
    }
}
