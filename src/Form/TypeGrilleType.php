<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeGrilleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('libelle', null, array('label' => 'Nom de la grille'))
        		->add('grille', 'collection', array(
            		'type' => new GrilleType(), 'allow_add' => true, 'allow_delete' => true, 'by_reference' => false, 
            		'cascade_validation' => true, 'options' => array('attr' => $options['attr'])
            	))->add('modeFonctionnement', null, array('empty_value' => 'Choisir le mode de fonctionnement ...', 'attr' => array('class' => 'chzn-select')));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            	'data_class' => 'App\Entity\TypeGrille', 'cascade_validation' => true
        	));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'type_grille';
    }
}
