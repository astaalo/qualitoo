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
        	->add('code', null, array('label' => 'Code '))
            ->add('libelle', null, array('label' => 'Nom complet '))
			->add('societe', null, array('label' => 'Societe ', 'attr' => array('placeholder' => 'Choisir une societe ...', 'class' => 'chzn-select')))
        	->add('typeStructure', null, array('label' => 'Type de structure ', 'attr' => array('placeholder' => 'Choisir un type de structure ...', 'class' => 'chzn-select')))
            ->add('parent', null, array('label' => 'Structure parente ', 'attr' => array('placeholder' => 'Choisir la parente rataché ...', 'class' => 'chzn-select')));
	}
	
	public function configureOptions(OptionsResolver $resolver)
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
