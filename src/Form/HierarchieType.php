<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Translatable\Fixture\Document\Personal\ArticleTranslation;
use Doctrine\ORM\EntityRepository;
	
class HierarchieType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
        $builder
        	->add('entiteOne', 'entity', array('class' => Structure::class, 'label' => 'Entité', 'placeholder' => 'Choisir une entité ...', 'attr' => array('class' => 'chzn-select')))
        	->add('entiteTwo', 'entity', array('class' => Structure::class, 'label' => 'Entité', 'placeholder' => 'Choisir une entité ...', 'attr' => array('class' => 'chzn-select')))
        	->add('entiteThree', 'entity', array('class' => Structure::class, 'label' => 'Entité', 'placeholder' => 'Choisir une entité ...', 'attr' => array('class' => 'chzn-select')))
        	->add('entiteFour', 'entity', array('class' => Structure::class, 'label' => 'Entité', 'placeholder' => 'Choisir une entité ...', 'attr' => array('class' => 'chzn-select')))
        	->add('entiteFive', 'entity', array('class' => Structure::class, 'label' => 'Entité', 'placeholder' => 'Choisir une entité ...', 'attr' => array('class' => 'chzn-select')));
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'App\Entity\Hierarchie'
		));
	}
	
	public function getName()
	{
		return 'hierarchie';
	}
}
