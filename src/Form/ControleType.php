<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\DataTransformer\EntityToIdTransformer;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class ControleType extends AbstractType
{	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('methodeControle', null, array('label' => 'Méthode de contrôle', 'placeholder' => 'Choisir la méthode de contrôle', 'attr' => array('class' => 'chzn-select')))
			->add('url', null, array('label' => 'Manuel de procédure', 'attr' => array('class' => 'medium')))
			->add('aTester', null, array('label' => 'A tester', 'required' => false,'attr' => array('class' => 'on_off_checkbox')))
			->add('porteur',  null, array('label' => 'Porteur ', 'placeholder' => 'Choisir le porteur', 'attr' => array('class' => 'chzn-select')))
			->add($builder->create('risque', HiddenType::class)->addModelTransformer(new EntityToIdTransformer($options['attr']['em'], '\App\Entity\Risque')))
			->add($builder->create('planAction', HiddenType::class)->addModelTransformer(new EntityToIdTransformer($options['attr']['em'], '\App\Entity\PlanAction')))
			->add('superviseur',  null, array('label' => 'Superviseur ', 'placeholder' => 'Choisir le superviseur', 'attr' => array('class' => 'chzn-select')))
			->add('typeControle',  null, array('label' => 'Type de contrôle ', 'placeholder' => 'Choisir le type de contrôle', 'attr' => array('class' => 'chzn-select')))
			->add('traitement',  null, array('label' => 'Traitement ', 'placeholder' => 'Choisir le traitement', 'attr' => array('class' => 'chzn-select')))
			->add('periodicite', null, array('placeholder'=> 'Choisir la périodicité ...', 'attr' => array('class' => 'chzn-select')))
			->add('grille', null, array('label'=>"Maturité",'placeholder'=> 'Choisir la maturité ...', 'attr' => array('class' => 'chzn-select')))
            //->add('causeOfRisque', null, array('property' => 'cause'))
            ->add('causeOfRisque', null)
            ->add('description', null, array('label' => 'Description du controle', 'attr'=>array('style'=>'width:95%')))
		;
		$builder->addEventListener(FormEvents::SUBMIT, array($this, 'addCauseOnEvent'));
		$builder->addEventListener(FormEvents::POST_SET_DATA, array($this, 'addCauseOnEvent'));
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
			if($event->getForm()==FormEvents::SUBMIT) {
				$event->getForm()->get('causeOfRisque')->submit($causeOfRisque ? $causeOfRisque->getId() : null);
			}
		}
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'App\Entity\Controle', 'allow_extra_fields' => true,
			'validation_groups' => function(FormInterface $form) {
				$groups = array('Default');
				if($form->getData()->inValidation()) {
					$groups[] = 'Validation';
				}
				if(!$form->getData()->inIdentification()) {
					$groups[] = 'Identification';
				}
				return $groups;
			}
		));
	}
	
	public function getName()
	{
		return 'controle';
	}
}
