<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Translatable\Fixture\Document\Personal\ArticleTranslation;
use Doctrine\ORM\EntityRepository;

class FamilleType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('libelle', null, array('label' => 'Nom de la famille'))
			->add('parent', null, array('label' => 'Famille parente', 'attr' => array('class' => 'chzn-select')))
			->add('description');
	}
	
	public function setDefaultOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'App\Entity\Famille'
		));
	}
	
	public function getName()
	{
		return 'famille';
	}
}
