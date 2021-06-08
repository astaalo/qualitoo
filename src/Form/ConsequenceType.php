<?php
/*
 * edit by @mariteuw
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Translatable\Fixture\Document\Personal\ArticleTranslation;
use Doctrine\ORM\EntityRepository;
use App\Form\CritereType;

class ConsequenceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('domaine', null ,array('label' => 'Domaine', 'empty_value' => 'Choisir un domaine'
            		,'attr' => array('class' => 'chzn-select')))
        	->add('critere', 'collection', array('type' => new CritereType()
        											));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Consequence'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'consequence';
    }
}
