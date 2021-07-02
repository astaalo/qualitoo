<?php
namespace App\SyntheseBundle\Repository;

use App\Entity\Risque;

/**
 * RisqueRepository
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EvaluationRepository extends DocumentRepository
{

	/**
	 * get Maturite, gravite and probabilite
	 * @param Risque $criteria
	 */
	public function getMaturiteGraviteProbabiliteByRisqueByYear($criteria) {
		$current_year = (int)date('Y');
		ini_set('mongo.long_as_object', 1);
		$years = array($current_year-2, $current_year-1, $current_year);
		$criteria = $criteria ? $criteria : new Risque();
		$queryBuilder = $this->createAggregationBuilder('e');
		$this->applyFilterByProfile($queryBuilder->match(), $criteria);
		$this->filterBuilder($queryBuilder->match(), $criteria);
		$queryBuilder->unWind('$impacts')->group()
			->field('id')->expression(
					$queryBuilder->expr()
						->field('menace')->expression('$menace')
						->field('annee')->expression('$annee')
						->field('gravite')->avg('$impacts.gravite')
						->field('probabilite')->avg('$probabilite')
						->field('maturite')->max('$maturite')
					)
				->project()
				->field('menace')->expression('$_id.menace.libelle')
				->field('gravite')->avg('$_id.gravite')
				->field('probabilite')->avg('$_id.probabilite')
				->field('maturite')->ceil('$_id.maturite')
				->field('annee')->avg('$_id.annee')
				->field('mId')->avg('$_id.menace.id')
				->excludeFields(['id']);
		$matchQuery = $queryBuilder->match();
		if(count($criteria->criticiteForKpi)>0) {
		 	$valeurs_criticite_criteria = array();
		 	foreach ($criteria->criticiteForKpi as $value) {
		 		for ($i=$value->getVmin(); $i<=$value->getVmax();$i++) {
		 			$valeurs_criticite_criteria [] = $i;
		 		}
		 	}
		 	$matchQuery->field('criticite')->in($valeurs_criticite_criteria);
		 }
		 if(count($criteria->graviteForKpi)>0) {
		 	$matchQuery->field('gravite')->in($criteria->graviteForKpi);
		 }
		 if(count($criteria->probaForKpi)>0) {
		 	$matchQuery->field('probabilite')->in($criteria->probaForKpi);
		 }
		 if ($criteria->anneeEvaluationDebut) {
		 	$matchQuery->field('annee')->gte($criteria->anneeEvaluationDebut);
		 }
		 if ($criteria->anneeEvaluationFin) {
		 	$matchQuery->field('annee')->lge($criteria->anneeEvaluationFin);
		 }
		 if(!$criteria->anneeEvaluationFin && !$criteria->anneeEvaluationFin) {
		 	$matchQuery->field('annee')->in($years);
		 }
		 $queryBuilder->sort(array('annee' => 1, 'menace.id' => -1));
		 return $queryBuilder;
	}
	
}
