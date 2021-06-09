<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\JoinColumns;

/**
 * Impact
 * @ORM\Table(name="impact")
 * @ORM\Entity(repositoryClass="App\Repository\ImpactRepository")
 */
class Impact
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
     *
     * @ORM\Column(name="date_creation", type="datetime", nullable=true)
     */
    private $dateCreation;

    /**
     * @var boolean
     *
     * @ORM\Column(name="etat", type="boolean", nullable=true)
     */
    private $etat = true;

    /**
     * @var Critere
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Critere", inversedBy="impact")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="critere_id", referencedColumnName="id")
     * })
     * @Assert\NotNull(groups={"RisqueValidation"}, message="Choisissez un critère s'il vous plait")
     */
    private $critere;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="RisqueHasImpact", mappedBy="impact")
     */
    private $risqueOfImpact;
    
    /**
     * @var EvaluationHasImpact
     * @ORM\OneToMany(targetEntity="EvaluationHasImpact", mappedBy="impact")
     */
    private $evaluationOfImpact;

    /**
     * @var Impact
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Impact")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="origine", referencedColumnName="id")
     * })
     */
    private $origine;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="EvaluationHasImpact", mappedBy="impact")
     */
    private $evaluation;
    
    /**
     * @var EvaluationHasImpact
     */
    public $currentEvaluation;
    
    /**
     * @var Domaine
     */
	public $domaine;
    
    /**
     * @var Cartographie
     */
    public $cartographie;
    
    /**
     * @var Menace
     */
    public $menace;
    

    public function __construct() {
    	$this->dateCreation = new \DateTime('NOW');
    }
    
    public function __toString(){
    	return $this->critere?$this->critere->getLibelle():' ';
    }
    /**
     * @return number
     */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return DateTime
	 */
	public function getDateCreation() {
		return $this->dateCreation;
	}
	
	/**
	 * @param \DateTime $dateCreation
	 * @return GrilleImpact
	 */
	public function setDateCreation($dateCreation) {
		$this->dateCreation = $dateCreation;
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function getEtat() {
		return $this->etat;
	}
	
	/**
	 * @param boolean $etat
	 * @return GrilleImpact
	 */
	public function setEtat($etat) {
		$this->etat = $etat;
		return $this;
	}
	
	/**
	 * @return Critere
	 */
	public function getCritere() {
		return $this->critere;
	}
	
	/**
	 * @param Critere $critere
	 * @return GrilleImpact
	 */
	public function setCritere($critere) {
		if($critere==null && $this->getCritere()) {
			$this->critere->removeGrilleImpact($this);
		}
		$this->critere = $critere;
		return $this;
	}
	
	/**
	 * @return Impact
	 */
	public function getOrigine() {
		return $this->origine;
	}
	
	/**
	 * @param Impact $origine
	 * @return Impact
	 */
	public function setOrigine($origine) {
		$this->origine = $origine;
		return $this;
	}
	
	/**
	 * @return Grille
	 */
	public function getGrille() {
		return $this->currentEvaluation?$this->currentEvaluation->getGrille():' ';
	}
	
	/**
	 * get risque
	 * @return Risque
	 */
	public function getRisque() {
		return $this->risqueOfImpact->getRisque();
	}
	
	/**
	 * set impact of evaluation
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getEvaluationOfImpact() {
		return $this->evaluationOfImpact;
	}
	
	/**
	 * get domaine
	 * @return DomaineImpact
	 */
	public function getDomaine() {
		return $this->domaine ? $this->domaine : ($this->currentEvaluation ? $this->currentEvaluation->getDomaine() : null);
	}

	/**
	 * get evaluation
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getEvaluation() {
		return $this->evaluation;
	}

	/**
	 * has evaluation
	 * @return bool
	 */
	public function hasEvaluation() {
		return $this->evaluation->count() > 0;
	}


    /**
     * Add risqueOfImpact
     *
     * @param RisqueHasImpact $risqueOfImpact
     * @return Impact
     */
    public function addRisqueOfImpact(RisqueHasImpact $risqueOfImpact)
    {
        $this->risqueOfImpact[] = $risqueOfImpact;
    
        return $this;
    }

    /**
     * Remove risqueOfImpact
     *
     * @param RisqueHasImpact $risqueOfImpact
     */
    public function removeRisqueOfImpact(RisqueHasImpact $risqueOfImpact)
    {
        $this->risqueOfImpact->removeElement($risqueOfImpact);
    }

    /**
     * Get risqueOfImpact
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRisqueOfImpact()
    {
        return $this->risqueOfImpact;
    }

    /**
     * Add evaluationOfImpact
     *
     * @param EvaluationHasImpact $evaluationOfImpact
     * @return Impact
     */
    public function addEvaluationOfImpact(EvaluationHasImpact $evaluationOfImpact)
    {
        $this->evaluationOfImpact[] = $evaluationOfImpact;
    
        return $this;
    }

    /**
     * Remove evaluationOfImpact
     *
     * @param EvaluationHasImpact $evaluationOfImpact
     */
    public function removeEvaluationOfImpact(EvaluationHasImpact $evaluationOfImpact)
    {
        $this->evaluationOfImpact->removeElement($evaluationOfImpact);
    }

    /**
     * Add evaluation
     *
     * @param EvaluationHasImpact $evaluation
     * @return Impact
     */
    public function addEvaluation(EvaluationHasImpact $evaluation)
    {
        $this->evaluation[] = $evaluation;
    
        return $this;
    }

    /**
     * Remove evaluation
     *
     * @param EvaluationHasImpact $evaluation
     */
    public function removeEvaluation(EvaluationHasImpact $evaluation)
    {
        $this->evaluation->removeElement($evaluation);
    }
}
