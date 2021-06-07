<?php
namespace App\Criteria;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UtilisateurCriteria extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('structure', 'entity', array('label'=>'Structure', 'property'=>'name', 'class'=>'\App\Entity\Structure', 'empty_value'=>'Choisir ...', 'required'=>false));
    }

    public function getName()
    {
        return 'utilisateur_criteria';
    }
}
