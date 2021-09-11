<?php
namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Utilisateur;

class DocumentType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('libelle', null, array('label' => 'Nom Document'))
				->add('typeDocument', null, array('label' => 'Type Document', 'attr' => array('class' => 'chzn-select')));
				if($builder->getData()->getId()==null) 
						$builder->add('file', FileType::class, array('label' => 'Document'));
						$builder
						->add('description', null, array('label' => 'Description', 'attr' => array('class' => 'fileupload')))
						->add('profils', ChoiceType::class, array('label' => 'Accessible par les profils :',
								'choices' => array( Utilisateur::ROLE_USER =>'Utilisateur Simple',
								Utilisateur::ROLE_Admin=> 'Administrateur', 
							), 
								'expanded' => true, 'multiple' => true))
						->add('tmpUtilisateur', EntityType::class, array(
		            		'multiple' => true,
		            		'class'=> Utilisateur::class,
		            		'label' => 'Accessible par les utilisateurs  :',
							'attr' => array('class' => 'chzn-select full')
		                ))
						;
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'App\Entity\Document'
		));
	}
	
	public function getName()
	{
		return 'document';
	}
}
