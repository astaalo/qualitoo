<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;

class AuditHasRisqueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
		            ->add('maturite', null, array('empty_value' => 'Maturité réel ...', 'label'=>'Maturité', 'attr' => array('class' => 'chzn-select chzn-done')))
        ;
           // $builder->addEventListener(FormEvents::SUBMIT, array($this, 'onSetData'));
    }
    /**
     * @param FormEvent $event
     */
    public function onSetData(FormEvent $event) {
    /*	if($event->getData() && null != $event->getData()->getRisque()) {
    					if($event->getName()==FormEvents::SUBMIT) {
    						$maturite = $event->getData()->getMaturite();
    						$event->getData()->setMaturite($maturite);
    					}
    	} */
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\AuditHasRisque',
        	'cascade_validation'=>true
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orange_mainbundle_audithasrisque';
    }
}
