<?php
namespace App\Form;

use App\Controller\BaseController;
use App\Entity\Grille;
use Psr\Container\ContainerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use App\Entity\ModeFonctionnement;
use App\Entity\TypeEvaluation;

class EvaluationHasCauseType extends AbstractType
{
    protected $ids;
    public function __construct(ContainerInterface $container)
    {
        $this->ids = $container->getParameter('ids');
    }
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('cause', null, array('placeholder' => 'Choisir une cause ...', 'label'=>'Choisir une cause', 'attr' => array('class' => 'chzn-select chzn-done')));
		$builder->addEventListener(FormEvents::SUBMIT, array($this, 'onSetData'));
		$builder->addEventListener(FormEvents::POST_SET_DATA, array($this, 'onSetData'));
	}
	
	/**
	 * @param FormEvent $event
	 */
	public function onSetData(FormEvent $event) {
		if($event->getData() && null != $evaluation = $event->getData()->getEvaluation()) {
			$risque = $evaluation->getRisque();
			//var_dump($event->getData()->getFinalGrille()->getId());exit;
			$event->getForm()->add('grille', null, array('query_builder' => function($er) use($risque) {
					return $er->createQueryBuilder('r')->innerJoin('r.typeGrille', 'tg')->innerJoin('tg.typeEvaluation', 'te')
						->where('tg.cartographie = :cartographie')->andWhere('te.id = :typeEvaluation')
						->setParameters(array('cartographie'=>$risque->getCartographie(), 'typeEvaluation'=>$this->ids['type_evaluation']['cause']));
				}, 'placeholder' => 'Choisir un niveau ...'));
			$event->getForm()->add('normalGrille', EntityType::class, array('label'=>'Normal','class' => Grille::class, 'query_builder' => function($er) use($risque) {
					return $er->createQueryBuilder('r')->where('r.typeGrille = :typeGrille')
						->setParameter('typeGrille', $risque->getTypeGrilleCauseBy(ModeFonctionnement::$ids['normal']));
				}, 'placeholder' => 'Choisir un niveau ...'));
			$event->getForm()->add('anormalGrille', EntityType::class, array('label'=>'Anormal','class' => Grille::class, 'query_builder' => function($er) use($risque) {
					return $er->createQueryBuilder('r')->where('r.typeGrille = :typeGrille')
						->setParameter('typeGrille', $risque->getTypeGrilleCauseBy(ModeFonctionnement::$ids['anormal']));
				}, 'placeholder' => 'Choisir un niveau ...'));
			if($event->getForm()->getName()==FormEvents::SUBMIT) {
				$grille = $event->getData()->getFinalGrille();
				$event->getData()->setGrille($grille);
				$event->getForm()->get('grille')->submit($grille ? $grille->getId() : null);
				$event->getForm()->get('normalGrille')->submit($event->getData()->getNormalGrille() ? $event->getData()->getNormalGrille()->getId() : null);
			}
		}
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'App\Entity\EvaluationHasCause',
				'cascade_validation'=>true,
				/*'validation_groups' => function(FormInterface $form) {
					$groups = array('Default');
					if($form->getData()->getRisque()->getId() && $form->getData()->getRisque()->hasToBeValidated()) {
						$groups [] = 'RisqueValidation';
					}
					if(!$form->getData()->getRisque()->getMenace() &&($form->getData()->getRisque()->getIdentification() && !$form->getData()->getRisque()->getIdentification()->getLibelle())) {
						$groups [] = 'RisqueIdentification';
					}
					return $groups;
				}*/
			));
	}
	
	public function getName()
	{
		return 'evaluation_has_cause';
	}
}
