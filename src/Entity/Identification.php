<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Societe
 *
 * @ORM\Table(name="identification")
 * @ORM\Entity
 */
class Identification {

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="libelle", type="string", length=100, nullable=true)
	 */
	private $libelle;
	
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="processus", type="string", length=100, nullable=true)
	 */
	private $processus;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="structure", type="string", length=100, nullable=true)
	 */
	private $structure;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="activite", type="string", length=100, nullable=true)
	 */
	private $activite;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="projet", type="string", length=100, nullable=true)
	 */
	private $projet;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="responsable", type="string", length=100, nullable=true)
	 */
	private $responsable;

	/**
	 * @var \Risque
	 *
	 * @ORM\OneToOne(targetEntity="Risque",inversedBy="identification")
     * @ORM\JoinColumn(name="risque_id", referencedColumnName="id")
	 */
	private $risque;

    /**
     * @var \Societe
     *
     * @ORM\ManyToOne(targetEntity="Societe")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="societe_id", referencedColumnName="id")
     * })
     */
    private $societe;

    /**
     * @var boolean
     *
     * @ORM\Column(name="etat", type="boolean", nullable=true)
     */
    private $etat = true;
    
	public function getId() {
		return $this->id;
	}
	public function setId() {
		 $this->id=null;
		 return $this;
	}
	
	public function getLibelle() {
		return $this->libelle;
	}
	
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}
	
	public function getProcessus() {
		return $this->processus;
	}
	
	public function setProcessus($processus) {
		$this->processus = $processus;
		return $this;
	}
	
	public function getStructure() {
		return $this->structure;
	}
	
	public function setStructure($structure) {
		$this->structure = $structure;
		return $this;
	}
	
	public function getActivite() {
		return $this->activite;
	}
	
	public function setActivite($activite) {
		$this->activite = $activite;
		return $this;
	}
	
	public function getProjet() {
		return $this->projet;
	}
	
	public function setProjet($projet) {
		$this->projet = $projet;
		return $this;
	}
	
	public function getResponsable() {
		return $this->responsable;
	}
	
	
	public function setResponsable($responsable) {
		$this->responsable = $responsable;
		return $this;
	}
	
	public function getRisque() {
		return $this->risque;
	}
	
	public function setRisque(Risque $risque) {
		$this->risque = $risque;
		return $this;
	}
	
	/**
	 * get soiciete
	 * @return Societe
	 */
	public function getSociete() {
		return $this->societe;
	}
	
	/**
	 * set societe
	 * @param \Societe $societe
	 * @return Identification
	 */
	public function setSociete($societe) {
		$this->societe = $societe;
		return $this;
	}
	
	public function getEtat() {
		return $this->etat;
	}
	
	public function setEtat($etat) {
		$this->etat = $etat;
		return $this;
	}
	
}
