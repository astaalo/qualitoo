<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class SocieteType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('libelle', null, array('label' => 'Libellé', 'attr' => array('class' => 'medium')))
			->add('file', null, array('label' => 'Icône', 'attr' => array('class' => 'file fileupload')))
			->add('famille', null, array('label' =>'Familles',
					'empty_value' => '-- Chosir les familles --', 'attr' => array('class' => 'chzn-select', 'multiple' => 'multiple')))
			->add('profilRisque', null, array('label' =>'Profil risque',
					'empty_value' => '-- Chosir les profils risques --', 'attr' => array('class' => 'chzn-select', 'multiple' => 'multiple')));
	}
	
	public function setDefaultOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'App\Entity\Societe'
		));
	}
	
	public function getName()
	{
		return 'societe';
	}
}
