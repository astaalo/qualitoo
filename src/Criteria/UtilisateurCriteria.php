<?php
namespace App\Criteria;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UtilisateurCriteria extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('profil', 'entity', array('label'=>'profil', 'property'=>'name', 'class'=>'\App\Entity\Profil', 'empty_value'=>'Choisir ...', 'required'=>false));
    }

    public function getName()
    {
        return 'utilisateur_criteria';
    }
}
