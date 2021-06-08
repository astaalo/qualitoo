<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Shtumi\UsefulBundle\Form\DataTransformer\EntityToPropertyTransformer;

class GrilleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('libelle', 'textarea', array('label' => 'Libellé'))
        	->add($builder->create('cartographie', 'hidden')->addModelTransformer(new EntityToPropertyTransformer($options['attr']['em'], '\App\Entity\Cartographie', 'libelle')))
        	->add('niveauCause', 'choice', array('empty_value' => 'Choisir un niveau ...', 'choices' => array(1 => 1, 2 => 2, 3 => 3, 4 => 4)))
        	->add('niveauImpact', 'choice', array('empty_value' => 'Choisir un niveau ...', 'choices' => array(1 => 1, 2 => 2, 3 => 3, 4 => 4)));
    }
	
    /**
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            	'data_class' => 'App\Entity\Grille'
       		));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'grille';
    }
}
