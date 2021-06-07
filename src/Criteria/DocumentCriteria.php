<?php
namespace App\Criteria;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentCriteria extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$year = $options['attr']['year']!=null? $options['attr']['year']: date('Y');
        $builder ->add('libelle')
        		 ->add('typeDocument')
        		 ->add('annee',null, array('data'=>$year));
    }
	
	public function setDefaultOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'App\Entity\Document',
				'csrf_protection' => false
			));
	}

    public function getName()
    {
        return 'document_criteria';
    }
}
