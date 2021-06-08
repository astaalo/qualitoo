<?php
namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\StructureRepository;

class ProcessusType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		
			->add('libelle', null, array('label' => 'Libellé'))
			->add('parent', null, array('label' => 'Processus parent', 'attr' => array('class' => 'chzn-select')))
			->add('origine', null, array('label' => 'Processus origine', 'empty_data' => 'Choisir le processus origine'
					,'attr' => array('class' => 'chzn-select')))
			->add('structure', EntityType::class, array('label' => 'Structure','class' => 'App\Entity\Structure', 'attr' => array('class' => 'chzn-select'), 'query_builder'=>function(StructureRepository $sr){
					          return $sr->filter();
					 }))
			->add('typeProcessus', null, array('empty_data' => 'Choisir un type de processus ...','attr'=>array('class'=>'chzn-select')))
			->add('description');
	}
	
	public function setDefaultOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'App\Entity\Processus'
		));
	}
	
	public function getName()
	{
		return 'processus';
	}
}
