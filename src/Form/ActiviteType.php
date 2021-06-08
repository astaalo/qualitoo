<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Translatable\Fixture\Document\Personal\ArticleTranslation;
use Doctrine\ORM\EntityRepository;

class ActiviteType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('libelle', null, array('label' => 'Libellé'))
			->add('processus', null, array('empty_value' => 'Chosir un processus', 'attr' => array('class' => 'chzn-select')))
			->add('origine', null, array('label' => 'Activité origine', 'empty_value' => 'Chosir l\'activité origine', 'attr' => array('class' => 'chzn-select')))
			->add('description');
	}
	
	public function setDefaultOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'App\Entity\Activite'
		));
	}
	
	public function getName()
	{
		return 'activite';
	}
}
