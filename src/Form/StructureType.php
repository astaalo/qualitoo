<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
//use Translatable\Fixture\Document\Personal\ArticleTranslation;
	
class StructureType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
        $builder
        	->add('code', null, array('label' => 'Nom '))
            ->add('libelle', null, array('label' => 'Nom complet '))
        	->add('typeStructure', null, array('label' => 'Type de structure ', 'attr' => array('empty_value' => 'Choisir un type de structure ...', 'class' => 'chzn-select')))
            ->add('parent', null, array('label' => 'Structure parente ', 'attr' => array('empty_value' => 'Choisir une structure ...', 'class' => 'chzn-select')));
	}
	
	public function setDefaultOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'App\Entity\Structure'
		));
	}
	
	public function getName()
	{
		return 'structure';
	}
}
