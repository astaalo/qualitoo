<?php
namespace App\Criteria;

use App\Entity\Cartographie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ControleCriteria extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        	->add('menace', 'entity', array('class'=>'\App\Entity\Menace', 'empty_value'=>'Chosir un risque ...', 'attr'=>array('class'=>'chzn-select')))
        	->add('cartographie', 'entity', array('class' => Cartographie::class, 'data_class' => '\App\Entity\Cartographie', 'expanded' => true))
        	->add('structurePorteur',  null, array('label' => 'Structure Porteur ', 'empty_value'=>'Choisir structure ...',  'attr' => array('class' => 'chzn-select')))
        	->add('porteur',  null, array('label' => 'Porteur ', 'empty_value'=>'Choisir un porteur ...',  'attr' => array('class' => 'chzn-select')))
        	->add('structureSuperviseur',  null, array('label' => 'Structure Superviseur ', 'empty_value'=>'Choisir une structure ...', 'attr' => array('class' => 'chzn-select')))
        	->add('superviseur',  null, array('label' => 'Superviseur ', 'empty_value'=>'Choisir un superviseur ...','attr' => array('class' => 'chzn-select')))
        	->add('typeControle',  null, array('label' => 'Type de contrôle ', 'empty_value'=>'Choisir un type ...',  'attr' => array('class' => 'chzn-select')))
        	->add('periodicite', null, array('empty_value'=> 'Choisir la périodicité ...', 'attr' => array('class' => 'chzn-select')))
        	->add('traitement',  null, array('label' => 'Traitement ', 'empty_value' => 'Choisir le traitement', 'attr' => array('class' => 'chzn-select')))
			->add('maturiteReels', 'entity', array('label'=>'Maturité aprés:' ,'expanded' => true, 'multiple' => true,'class' => '\App\Entity\Maturite'))
			->add('maturiteTheoriques', 'entity', array('label'=>'Maturité avant' ,'expanded' => true, 'multiple' => true,'class' => '\App\Entity\Maturite'))        ;
        
    }
	
	public function setDefaultOptions(OptionsResolver $resolver)
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
