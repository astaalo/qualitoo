<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjetType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('libelle', null, array('label' => 'Libellé'))
        	->add('dateDebut', null, array('input' => 'datetime', 'widget' => 'single_text', 'format' => 'dd-MM-y'))
        	->add('dateFin', null, array('input' => 'datetime', 'widget' => 'single_text', 'format' => 'dd-MM-y'))
        	->add('processus')
        	->add('utilisateur')
        	->add('description');
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Projet'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'projet';
    }
}
