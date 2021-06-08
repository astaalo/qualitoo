<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Translatable\Fixture\Document\Personal\ArticleTranslation;
use Doctrine\ORM\EntityRepository;

class AvancementType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		
			->add('description')
			->add('etatAvancement', null, array(
					'empty_value' => 'Chosir l\'état d\'avancement'
					,'attr' => array('class' => 'chzn-select')));
	}
	
	public function setDefaultOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'App\Entity\Avancement'
		));
	}
	
	public function getName()
	{
		return 'avancement';
	}
}
