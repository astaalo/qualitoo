<?php
namespace App\SyntheseBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Evaluation
 * @MongoDB\Document(collection="evaluation", repositoryClass="Orange\SyntheseBundle\Repository\EvaluationRepository")
 */
class Evaluation {
	
	/**
	 * @MongoDB\Id
	 */
	private $id;
	
	/**
     * @var string
     * @MongoDB\Field(type="string")
     */
	private $menace;

	/**
     * @var string
     * @MongoDB\Field(type="string")
     */
	private $structure;

	/**
     * @var string
     * @MongoDB\Field(type="string")
     */
	private $processus;
	
	/**
	 * @var integer
	 * @MongoDB\Field(type="integer")
	 */
	private $cartographie;
	
	/**
	 * @var integer
	 * @MongoDB\Field(type="integer")
	 */
	private $direction;
	
	/**
	 * @var integer
	 * @MongoDB\Field(type="integer")
	 */
	private $societe;

	/**
     * @var integer
     * @MongoDB\Field(type="integer")
     */
	private $activite;

	/**
     * @var integer
     * @MongoDB\Field(type="integer")
     */
	private $projet;

	/**
     * @var integer
     * @MongoDB\Field(type="integer")
     */
	private $site;

	/**
	 * @var array
     * @MongoDB\Field(type="string")
	 */
	private $causes;

	/**
	 * @var integer
     * @MongoDB\Field(type="integer")
	 */
	private $criticite;

	/**
	 * @var integer 
     * @MongoDB\Field(type="integer")
	 */
	private $probabilite;


	/** 
	 * Constructor 
	 **/
	public function __construct() {
		
	}
	
	/**
	 * Get id
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * get menace
	 * @return string
	 */
	public function getMenace() {
		return $this->menace;
	}

	/**
	 * @param string $menace        	
	 * @return Synthese
	 */
	public function setMenace($menace) {
		$this->menace = $menace;
		return $this;
	}
	
	/**
	 * get structure
	 * @return string
	 */
	public function getStructure() {
		return $this->structure;
	}

	/**
	 * @param string $structure        	
	 * @return Synthese
	 */
	public function setStructure($structure) {
		$this->structure = $structure;
		return $this;
	}

	/**
	 * get processus
	 * @return string
	 */
	public function getProcessus() {
		return $this->processus;
	}

	/**
	 * @param string $processus        	
	 * @return Synthese
	 */
	public function setProcessus($processus) {
		$this->processus = $processus;
		return $this;
	}

	/**
	 * get cartographie
	 * @return Orange\MainBundle\Entity\Cartographie
	 */
	public function getCartographie() {
		return $this->cartographie;
	}

	/**
	 * @param integer $cartographie
	 * @return Synthese
	 */
	public function setCartographie($cartographie) {
		$this->cartographie = $cartographie;
		return $this;
	}

	/**
	 * get activite
	 * @return string
	 */
	public function getActivite() {
		return $this->activite;
	}

	/**
	 * @param string $activite
	 * @return Synthese
	 */
	public function setActivite($activite) {
		$this->activite = $activite;
		return $this;
	}

	/**
	 * get projet
	 * @return string
	 */
	public function getProjet() {
		return $this->projet;
	}

	/**
	 * @param string $projet
	 * @return Synthese
	 */
	public function setProjet($projet) {
		$this->projet = $projet;
		return $this;
	}

	/**
	 * get site
	 * @return string
	 */
	public function getSite() {
		return $this->site;
	}

	/**
	 * @param string $site
	 * @return Synthese
	 */
	public function setSite($site) {
		$this->site = $site;
		return $this;
	}

	/**
	 * get gravite
	 * @return integer
	 */
	public function getGravite() {
		return $this->gravite;
	}

	/**
	 * @param string $gravite
	 * @return Synthese
	 */
	public function setGravite($gravite) {
		$this->gravite = $gravite;
		return $this;
	}

	/**
	 * get criticite
	 * @return integer
	 */
	public function getCriticite() {
		return $this->criticite;
	}

	/**
	 * @param string $criticite
	 * @return Synthese
	 */
	public function setCriticite($criticite) {
		$this->criticite = $criticite;
		return $this;
	}

	/**
	 * get probabilite
	 * @return integer
	 */
	public function getProbabilite() {
		return $this->probabilite;
	}

	/**
	 * @param string $probabilite
	 * @return Synthese
	 */
	public function setProbabilite($probabilite) {
		$this->probabilite = $probabilite;
		return $this;
	}

	/**
	 * get annee
	 * @return string
	 */
	public function getAnnee() {
		return $this->annee;
	}

	/**
	 * @param string $annee
	 * @return Synthese
	 */
	public function setAnnee($annee) {
		$this->annee = $annee;
		return $this;
	}

	/**
	 * get societe
	 * @return string
	 */
	public function getSociete() {
		return $this->societe;
	}

	/**
	 * @param string $societe
	 * @return Synthese
	 */
	public function setSociete($societe) {
		$this->societe = $societe;
		return $this;
	}
	
}
