<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Translatable\Fixture\Document\Personal\ArticleTranslation;
use Doctrine\ORM\EntityRepository;
use App\Form\DataTransformer\StructureTransformer;

class IdentificationType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('libelle', null, array('label' => 'Libellé', 'attr' => array('widget_help' => 'Veuillez saisir le nom du risque dans la zone de texte')))
			->add('processus', null, array('attr' => array('widget_help' => 'Veuillez saisir le processus dans la zone de texte')))
			->add('responsable', null, array('attr' => array('label_help' => 'Respnsable', 'widget_help' => 'Saisir le Responsable ')))
			->add('projet', null, array('attr' => array('widget_help' => 'Veuillez saisir le projet dans la zone de texte')))
			->add('activite', null, array('attr' => array('widget_help' => 'Veuillez saisir l\'activité dans la zone de texte')));
			
		$builder->add('structure', null, array('attr' => array('widget_help' => 'Veuillez saisir la strucutre dans la zone de texte')));
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'App\Entity\Identification'
		));
	}
	
	public function getName()
	{
		return 'identification';
	}
}
