<?php
namespace Orange\MainBundle\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\Container;
use Orange\MainBundle\Query\RisqueMetierQuery;
use Orange\MainBundle\Entity\Chargement;
use Orange\MainBundle\Query\RisqueSSTEQuery;
use Orange\MainBundle\Query\RisqueProjetQuery;

class Loader {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	
 	/**
	 * @var string
	 */
	protected $web_dir;
	
	/**
	 * @var array
	 */
	protected $ids;
	
	protected $container;

	/**
	 * @param \Symfony\Component\DependencyInjection\Container $container
	 */
	public function __construct($container, $web_dir,$ids) {
		$this->container = $container;
		$this->em = $container->get('doctrine.orm.entity_manager');
		$this->web_dir = $web_dir;
		$this->ids =$ids;
	}
	
	/**
	 * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
	 * @param Chargement $chargement 
	 * @return boolean
	 */
	public function loadRisque($file,$current_user,$chargement) {
		/**		 @var \Doctrine\DBAL\Connection $connection */
		$connection = $this->container->get('database_connection');
		$carto = $chargement->getCartographie() ? $chargement->getCartographie()->getId():null;
		try {
			$connection->setAutoCommit(false);
			$connection->beginTransaction();
			$query = null;
			switch ($carto){
				case $this->ids['carto']['metier']:
					$query = $this->loadRisqueMetier($file, $current_user,$chargement);
					break;
				case $this->ids['carto']['projet']:
					$query = $this->loadRisqueProjet($file, $current_user, $chargement);
					break;
				case $this->ids['carto']['sst']:
					$query = $this->loadRisqueSSTE($file, $current_user,$chargement);
					break;
				case $this->ids['carto']['environnement']:
					$query = $this->loadRisqueSSTE($file, $current_user,$chargement);
					break;
				default:
					return ;
					break;
			}
			if($query) {
				$query->loadImpactOfRisk();
			}
			$connection->commit();
		} catch (ConnectionException $e) {
			$connection->rollBack();
			throw new \Exception("Format de fichier invalide!", 500);
		}
		$connection->setAutoCommit(true);
	}
	
	/**
	 * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
	 * @param Chargement $chargement
	 * @return boolean
	 */
	public function loadRisqueMetier($file,$current_user,$chargement) {
		$query = new RisqueMetierQuery($this->em->getConnection());
		$nextId = $this->em->getRepository('OrangeMainBundle:Risque')->getNextId();
		$query->createTable($nextId);
		$query->loadTable($chargement, $file->getPathname(), $this->web_dir, $nextId);
        $query->updateTable($chargement,$this->ids,$this->em);
		$query->migrateData($current_user, $this->em,$chargement,$this->ids);
		$query->calculFinal($chargement, $this->ids);
		return $query;
	}
	
	/**
	 * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
	 * @param Chargement $chargement
	 * @return boolean
	 */
	public function loadRisqueProjet($file, $current_user,$chargement) {
		$query = new RisqueProjetQuery($this->em->getConnection());
		$nextId = $this->em->getRepository('OrangeMainBundle:Risque')->getNextId();
		$query->createTable($nextId);
	    $query->loadTable($file->getPathname(), $this->web_dir, $nextId);
        $query->updateTable($chargement,$this->ids, $this->em);
		$query->migrateData($current_user, $this->em, $chargement, $this->ids);
		$query->calculFinal($chargement, $this->ids);
		return $query;
	}
	
	/**
	 * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
	 * @param Chargement $chargement
	 * @return boolean
	 */
	public function loadRisqueSSTE($file,$current_user,$chargement) {
		$query = new RisqueSSTEQuery($this->em->getConnection());
		$nextId = $this->em->getRepository('OrangeMainBundle:Risque')->getNextId();
		$query->createTable($nextId);
		$query->loadTable($file->getPathname(), $this->web_dir,$nextId);
 		$query->updateTable($chargement,$this->ids,$this->em,$current_user);
		$query->migrateData($current_user, $this->em,$chargement,$this->ids);
		$query->calculFinal($chargement, $this->ids);
		return $query;
	}
	
}