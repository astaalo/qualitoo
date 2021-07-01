<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Translatable\Fixture\Document\Personal\ArticleTranslation;
use Doctrine\ORM\EntityRepository;

class ExecutionType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('valide', null, array('label' =>"Contôle effectué", 'attr' => array('class' => 'on_off_checkbox')))
			->add('file', null, array('label' => 'Preuve', 'attr' => array('class' => 'fileupload')))
			->add('commentaire');
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'App\Entity\Execution'
		));
	}

	public function getName()
	{
		return 'execution';
	}
}
