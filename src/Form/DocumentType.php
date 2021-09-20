<?php
namespace App\Form;

use App\Entity\Utilisateur;
use App\Repository\ProfilRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DocumentType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('libelle', null, array('label' => 'Nom Document'))
				->add('typeDocument', null, array('label' => 'Type Document', 'attr' => array('class' => 'chzn-select')))
				->add('rubrique', null, array('label' => 'Rubrique', 'attr' => array('class' => 'chzn-select')))
				->add('theme', null, array('label' => 'Theme', 'attr' => array('class' => 'chzn-select')));
				if($builder->getData()->getId()==null) {
						$builder
						->add('file', FileType::class, array('label' => 'File', 'attr' => array('class' => 'file fileupload')))
						->add('description', null, array('label' => 'Description'))
						->add('profil', EntityType::class, array('label' => 'Profil','class' => 'App\Entity\Profil', 'attr' => array('class' => 'chzn-select'), 'query_builder' => function (ProfilRepository $er) {
							return $er->createQueryBuilder('u')
								->orderBy('u.libelle', 'ASC');
						},))
						//->add('tmpUtilisateur', EntityType::class, array(
		            	//	'multiple' => true,
		            	//	'class'=> Utilisateur::class,
		            	//	'label' => 'Accessible par les utilisateurs  :',
						//	'attr' => array('class' => 'chzn-select full')
		                //))
						;
				}
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
