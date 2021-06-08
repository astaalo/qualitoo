<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
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
						$builder->add('file', 'file', array('label' => 'Document'));
				$builder
						->add('description', null, array('label' => 'Description', 'attr' => array('class' => 'fileupload')))
						->add('profils', 'choice', array('label' => 'Accessible par les profils :',
														 'choices' => array( Utilisateur::ROLE_USER =>'Tout le monde',
														 					 Utilisateur::ROLE_AUDITEUR=> 'Auditeur', 
																		     Utilisateur::ROLE_SUPERVISEUR=> 'Superviseur'
																		 ), 
								'expanded' => true, 'multiple' => true))
						->add('tmpUtilisateur', 'entity', array(
		            		'multiple' => true,
		            		'class'=>'OrangeMainBundle:Utilisateur',
		            		'label' => 'Accessible par les utilisateurs  :',
							'attr' => array('class' => 'chzn-select full')
		                ))
						;
	}
	
	public function setDefaultOptions(OptionsResolver $resolver)
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
