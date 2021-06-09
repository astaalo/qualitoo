<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Avancement
 *
 * @ORM\Table(name="avancement")
 * @ORM\Entity
 */
class Avancement
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="date_action", type="datetime", nullable=false)
     */
    private $dateAction;
	
    /**
     * @var String
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="etat", columnDefinition="tinyint(1) default 0")
     */
    private $etat;
    
    /**
     * @var \Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="acteur", referencedColumnName="id")
     * })
     */
    private $acteur;

    /**
     * @var \PlanAction
     *
     * @ORM\ManyToOne(targetEntity="PlanAction")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="plan_action_id", referencedColumnName="id")
     * })
     */
    private $planAction;
    
    /**
     * @var \EtatAvancement
     *
     * @ORM\ManyToOne(targetEntity="EtatAvancement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="etat_avancement_id", referencedColumnName="id")
     * })
     */
    private $etatAvancement;
    
    public function __construct() {
    	$this->dateAction = new \DateTime('NOW');
    }
    
    
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return DateTime
	 */
	public function getDateAction() {
		return $this->dateAction;
	}
	
	/**
	 * @param \DateTime $dateAction
	 * @return Avancement
	 */
	public function setDateAction($dateAction) {
		$this->dateAction = $dateAction;
		return $this;
	}
	
	/**
	 * @return number
	 */
	public function getEtat() {
		return $this->etat;
	}
	
	/**
	 * @param integer $etat
	 * @return Avancement
	 */
	public function setEtat($etat) {
		$this->etat = $etat;
		return $this;
	}
	
	/**
	 * @return Utilisateur
	 */
	public function getActeur() {
		
		return $this->acteur;
	}
	
	/**
	 * @param \Utilisateur $acteur
	 * @return Avancement
	 */
	 public function setActeur($acteur) {
		$this->acteur = $acteur;
		return $this;
	}
	
	/**
	 * @return PlanAction
	 */
	public function getPlanAction() {
		return $this->planAction;
	}
	
	/**
	 * @param \PlanAction $planAction
	 * @return Avancement
	 */
	public function setPlanAction($planAction) {
		$this->planAction = $planAction;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * @param string $description
	 * @return Avancement
	 */
	public function setDescription($description) {
		$this->description = $description;
		return $this;
	}
	public function getEtatAvancement() {
		return $this->etatAvancement;
	}
	public function setEtatAvancement($etatAvancement) {
		$this->etatAvancement = $etatAvancement;
		return $this;
	}
	
	/**
	 * Get libelle
	 *
	 * @return string
	 */
	public function __toString(){
			
		return $this->description;
	}
	
	
}
