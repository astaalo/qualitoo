<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\CallbackValidator;
use Symfony\Component\Form\FormBuilderInterface;

class ImportType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			    ->add('file', FileType::class, array('label' => 'Fichier', 'required' =>  true, 'attr' => array('accept' => 'text/csv')))
				->add('add', SubmitType::class, array('label' => 'Importer', 'attr' => array('class' => 'btn btn-warning')))
                ->add('cancel', ButtonType::class, array('label' => 'Réinitialiser', 'attr' => array('class' => 'btn btn-die cancel')));
	}
	
	/**
	 * @return string
	 */
	public function getName()
	{
		return 'orange_mainbundle_loading';
	}
}
