<?php
namespace App\Criteria;

use App\Entity\Cartographie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ControleCriteria extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        	->add('menace', EntityType::class, array('class'=>'App\Entity\Menace', 'placeholder'=>'Chosir un risque ...', 'attr'=>array('class'=>'chzn-select')))
        	->add('cartographie', EntityType::class, array('class' => Cartographie::class, 'data_class' => 'App\Entity\Cartographie', 'expanded' => true))
        	->add('structurePorteur',  null, array('label' => 'Structure Porteur ', 'placeholder'=>'Choisir structure ...',  'attr' => array('class' => 'chzn-select')))
        	->add('porteur',  null, array('label' => 'Porteur ', 'placeholder'=>'Choisir un porteur ...',  'attr' => array('class' => 'chzn-select')))
        	->add('structureSuperviseur',  null, array('label' => 'Structure Superviseur ', 'placeholder'=>'Choisir une structure ...', 'attr' => array('class' => 'chzn-select')))
        	->add('superviseur',  null, array('label' => 'Superviseur ', 'placeholder'=>'Choisir un superviseur ...','attr' => array('class' => 'chzn-select')))
        	->add('typeControle',  null, array('label' => 'Type de contrôle ', 'placeholder'=>'Choisir un type ...',  'attr' => array('class' => 'chzn-select')))
        	->add('periodicite', null, array('placeholder'=> 'Choisir la périodicité ...', 'attr' => array('class' => 'chzn-select')))
        	->add('traitement',  null, array('label' => 'Traitement ', 'placeholder' => 'Choisir le traitement', 'attr' => array('class' => 'chzn-select')))
			->add('maturiteReels', EntityType::class, array('label'=>'Maturité aprés:' ,'expanded' => true, 'multiple' => true,'class' => 'App\Entity\Maturite'))
			->add('maturiteTheoriques', EntityType::class, array('label'=>'Maturité avant' ,'expanded' => true, 'multiple' => true,'class' => 'App\Entity\Maturite'))        ;
        
    }
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
            'data_class' => 'App\Entity\Controle',
            'csrf_protection' => false
        ));
	}

    public function getName()
    {
        return 'controle_criteria';
    }
}
