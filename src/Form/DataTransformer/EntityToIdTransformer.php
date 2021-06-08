<?php
namespace App\Form\DataTransformer;

use Shtumi\UsefulBundle\Form\DataTransformer\EntityToIdTransformer as Transformer;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\ErrorMappingException;

class EntityToIdTransformer extends Transformer {
	

	public function transform($entity)
	{
	
		if (null === $entity || '' === $entity) {
			return '';
		}
		if (!is_object($entity)) {
			throw new UnexpectedTypeException($entity, 'object');
		}
		if (!$this->unitOfWork->isInIdentityMap($entity)) {
			throw new ErrorMappingException('Entities passed to the choice field must be managed');
		}
	
		return $entity->getId();
	}
}