<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use App\Form\DataTransformer\EntityToIdTransformer;

class PlanActionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$type_statut= $options['attr']['type_statut'];
        $builder->add($builder->create('risque', HiddenType::class)->addModelTransformer(new EntityToIdTransformer($options['attr']['em'], '\App\Entity\Risque')))
			->add('libelle', null, array('label' => 'Description du plan d\'action'))
        	->add('statut', null, array('attr' => array('class' => 'chzn-select'), 'query_builder' => function($er) use($type_statut) {
        		return $er->createQueryBuilder('r')->where('r.type = :type')->andWhere('r.etat = :etat')
        			->setParameters(array('type' => $type_statut, 'etat' => true));
        	}))->add($builder->create('controle', HiddenType::class)->addModelTransformer(new EntityToIdTransformer($options['attr']['em'], '\App\Entity\Controle')))
        	->add('dateDebut', null, array('input' => 'datetime', 'widget' => 'single_text', 'format' => 'dd-MM-y'))
        	->add('dateFin', null, array('input' => 'datetime', 'widget' => 'single_text', 'format' => 'dd-MM-y'))
        	->add('porteur', null, array('placeholder' => 'Choisir le porteur ...', 'attr' => array('class' => 'chzn-select')))
        	->add('superviseur', null, array('placeholder' => 'Choisir le superviseur ...', 'attr' => array('class' => 'chzn-select')))
            //->add('causeOfRisque', null, array('property' => 'cause'))
            ->add('causeOfRisque', null)
            ->add('description', null, array('label' => 'Etat d\'avancement'))
        	;
		$builder->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) { 
				$this->addCauseOnEvent($event);
			});
		$builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) { 
				$this->addCauseOnEvent($event); 
			});
    }
	
	/**
	 * @param FormEvent $event
	 */
	public function addCauseOnEvent(FormEvent $event) {
		if(null != $risque = $event->getData()->getRisque()) {
			$causeOfRisque = $event->getData()->getCauseOfRisque();
			$event->getForm()->add('causeOfRisque', null, array('placeholder' => 'Choisir une cause ...', 'query_builder' => function($er) use($risque) {
					return $er->createQueryBuilder('r')->where('r.risque = :risque')->setParameter('risque', $risque);
				}, 'label' => 'Nom de la cause', 'attr' => array('class' => 'chzn-select full')
			));
			if($event->getForm()->getName()==FormEvents::SUBMIT) {
				$event->getForm()->get('causeOfRisque')->submit($causeOfRisque ? $causeOfRisque->getId() : null);
			}
		}
	}
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\PlanAction',
			'validation_groups' => function(FormInterface $form) {
				$groups = array('Default');
				if($form->getData()->inValidation()) {
					$groups [] = 'Validation';
				}
				if(!$form->getData()->inIdentification()) {
					$groups [] = 'Identification';
				}
				return $groups;
			}
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'planaction';
    }
}
