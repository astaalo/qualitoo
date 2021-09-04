<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * EvaluationHasImpact
 *
 * @ORM\Table(name="evaluation_has_impact")
 * @ORM\Entity
 */
class EvaluationHasImpact
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;
    
    /**
     * @var Evaluation
     *
     * @ORM\ManyToOne(targetEntity="Evaluation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="evaluation_id", referencedColumnName="id")
     * })
     */
    private $evaluation;

    /**
     * @var Impact
     *
     * @ORM\ManyToOne(targetEntity="Impact")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="impact_id", referencedColumnName="id")
     * })
     */
    private $impact;

    /**
     * @var Grille
     *
     * @ORM\ManyToOne(targetEntity="Grille")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="grille_id", referencedColumnName="id")
     * })
     */
    private $grille;
    
    /**
     * @var Domaine
     */
    private $domaine;
    

    /**
     * get id
     * @return integer
     */
    public function getId() {
    	return $this->id;
    }
    
    /**
     * set id
     * @return integer
     */
    public function setId($id) {
    	 $this->id=null;
    }
    
    /**
     * @return Evaluation
     */
	public function getEvaluation() {
		return $this->evaluation;
	}
	
	/**
	 * @param \Evaluation $evaluation
	 * @return EvaluationHasImpact
	 */
	public function setEvaluation($evaluation) {
		$this->evaluation = $evaluation;
		return $this;
	}
	
	/**
	 * @return Impact
	 */
	public function getImpact() {
		return $this->impact;
	}
	
	/**
	 * @param Impact $impact
	 * @return EvaluationHasImpact
	 */
	public function setImpact($impact) {
		$impact->currentEvaluation = $this;
		$this->impact = $impact;
		return $this;
	}
	
	/**
	 * @return Grille
	 */
	public function getGrille() {
		return $this->grille;
	}
	
	/**
	 * @param \Grille $grille
	 * @return EvaluationHasImpact
	 */
	public function setGrille($grille) {
		$this->grille = $grille;
		return $this;
	}
	
	/**
	 * @return DomaineImpact
	 */
	public function getDomaine() {
		return $this->domaine 
			? $this->domaine 
			: ($this->impact ? ($this->impact->getCritere() ? $this->impact->getCritere()->getDomaine() : null) : null);
	}
	
	/**
	 * set domaine
	 * @param Domaine $domaine
	 * @return RisqueHasImpact
	 */
	public function setDomaine($domaine) {
		$this->domaine = $domaine;
		return $this;
	}
	
	/**
	 * @Assert\Callback(groups={"evaluation"})
	 */
	public function validate(ExecutionContextInterface $context) {
		if($this->impact && $this->impact->getCritere() && $this->grille==null) {
			 $context->buildViolation("Choisissez un niveau s'il vous plait")->atPath('grille')->addViolation();
		}
	}


}
