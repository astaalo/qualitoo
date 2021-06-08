<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Translatable\Fixture\Document\Personal\ArticleTranslation;
use Doctrine\ORM\EntityRepository;

class ColonneType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('extraction')
			->add('typeColonne')
			->add('etat', null, array('attr' => array('class' => 'on_off_checkbox')));
	}
	
	public function setDefaultOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'App\Entity\Colonne'
		));
	}
	
	public function getName()
	{
		return 'colonne';
	}
}
