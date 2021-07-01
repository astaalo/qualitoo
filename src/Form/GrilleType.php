<?php
namespace App\Form;

use App\Entity\Cartographie;
use App\Form\DataTransformer\EntityToPropertyTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
//use Shtumi\UsefulBundle\Form\DataTransformer\EntityToPropertyTransformer;

class GrilleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', TextareaType::class, array('label' => 'Libellé'))
        	->add($builder->create('cartographie', HiddenType::class)->addModelTransformer(new EntityToPropertyTransformer($options['attr']['em'], Cartographie::class, 'libelle')))
        	->add('niveauCause', ChoiceType::class, array('placeholder' => 'Choisir un niveau ...', 'choices' => array(1 => 1, 2 => 2, 3 => 3, 4 => 4)))
        	->add('niveauImpact', ChoiceType::class, array('placeholder' => 'Choisir un niveau ...', 'choices' => array(1 => 1, 2 => 2, 3 => 3, 4 => 4)));
    }
	
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
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
