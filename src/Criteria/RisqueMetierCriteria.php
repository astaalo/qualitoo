<?php
namespace App\Criteria;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\StructureRepository;

class RisqueMetierCriteria extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('structure', 'entity', array(
				'class' => '\App\Entity\Structure', 'label' => 'Structure', 'empty_value' => 'Choisir une structure ...', 'query_builder'=>function($sr) {
					          return $sr->filter();
					 },
				'attr' => array(
						'class' => 'chzn-select', 'label_help' => 'Structure à laquelle l\'activité est rattachée',
						'widget_help' => 'Cliquer puis rechercher et choisir une structure dans la liste'
				)
			));
		$builder->add('direction', 'entity', array(
				'label' => 'Direction', 'class' => '\App\Entity\Structure', 'query_builder' => function(StructureRepository $er){
							return $er->listAllDirectionBySociete();
						}, 'attr' => array(
							'class' => 'chzn-select', 'label_help' => 'Direction', 'widget_help' => 'Choisir une direction dans la liste'
					), 'empty_value' => 'Choisir la direction ...'
			))
        	->add('processus', null, array('empty_value'=>'Chosir un processus ...', 'attr'=>array('class'=>'chzn-select')))
        	->add('activite', null, array('empty_value'=>'Chosir une activité ...', 'attr'=>array('class'=>'chzn-select')));
    }
	
	public function setDefaultOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'App\Entity\RisqueMetier',
				'csrf_protection' => false
			));
	}

    public function getName()
    {
        return 'risquemetier_criteria';
    }
}
