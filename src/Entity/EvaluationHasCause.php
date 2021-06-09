<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * EvaluationHasCause
 *
 * @ORM\Table(name="evaluation_has_cause")
 * @ORM\Entity
 */
class EvaluationHasCause
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
     * @ORM\ManyToOne(targetEntity="Evaluation", cascade={"persist", "merge"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="evaluation_id", referencedColumnName="id")
     * })
     */
    private $evaluation;

    /**
     * @var Cause
     *
     * @ORM\ManyToOne(targetEntity="Cause")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cause_id", referencedColumnName="id")
     * })
     */
    private $cause;
    
    /**
     * @var ModeFonctionnement
     * @ORM\ManyToOne(targetEntity="ModeFonctionnement")
     * @ORM\JoinColumns({
     *    @ORM\JoinColumn(name="mode_fonctionnement_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $modeFonctionnement;

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
     * @var \Maturite
     * @ORM\ManyToOne(targetEntity="Maturite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="maturite_id", referencedColumnName="id")
     * })
     */
    private $maturite;
    
    /**
     * @var Grille
     */
    public $normalGrille;
    
    /**
     * @var Grille
     */
    public $anormalGrille;
    
    
    public function __construct(){
    }

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
	 * @param Evaluation $evaluation
	 * @return EvaluationHasCause
	 */
	public function setEvaluation($evaluation) {
		$this->evaluation = $evaluation;
		return $this;
	}
	
	/**
	 * @return Cause
	 */
	public function getCause() {
		return $this->cause;
	}
	
	/**
	 * @param Cause $cause
	 * @return EvaluationHasCause
	 */
	public function setCause($cause) {
		$this->cause = $cause;
		return $this;
	}

    /**
     * Set modeFonctionnement
     * @param ModeFonctionnement $modeFonctionnement
     * @return EvaluationHasCause
     */
    public function setModeFonctionnement(ModeFonctionnement $modeFonctionnement = null)
    {
        $this->modeFonctionnement = $modeFonctionnement;
        return $this;
    }

    /**
     * Get modeFonctionnement
     * @return ModeFonctionnement
     */
    public function getModeFonctionnement()
    {
    	return  $this->modeFonctionnement;
    }
	
	/**
	 * @return Grille
	 */
	public function getGrille() {
		return $this->grille;
	}
	
	/**
	 * @param Grille $grille
	 * @return EvaluationHasCause
	 */
	public function setGrille($grille) {
		$this->grille = $grille;
		return $this;
	}
	
	public function getNote(){
		return $this->grille->getValeur()<3 ? (4 -$this->grille->getValeur()) : 1;
	}
	
	/**
	 * @param Grille $normalGrille
	 * @return Grille
	 */
	public function getNormalGrille() {
		return $this->normalGrille;
	}
	
	/**
	 * @param Grille $normalGrille
	 * @return EvaluationHasCause
	 */
	public function setNormalGrille($normalGrille) {
		$this->normalGrille = $normalGrille;
		return $this;
	}
	
	/**
	 * @return Grille
	 */
	public function getAnormalGrille() {
		return $this->anormalGrille;
	}
	
	/**
	 * @param Grille $anormalGrille
	 * @return EvaluationHasCause
	 */
	public function setAnormalGrille($anormalGrille) {
		$this->anormalGrille = $anormalGrille;
		return $this;
	}
	
	public function getFinalGrille() {
		$grille = $this->grille;
		if($this->evaluation->getRisque()->isPhysical()) {
			//var_dump($this->grille->getId());exit;
			$grille = $this->normalGrille ? $this->normalGrille : ($this->anormalGrille ? $this->anormalGrille : null);
		}
		return $grille;
	}
	
	/**
	 * @Assert\Callback(groups={"evaluation"})
	 */
	public function validateGrille(ExecutionContextInterface $context) {
		$message="Choisissez un niveau s'il vous plait";
		if($this->evaluation->getRisque()->isPhysical() && !$this->anormalGrille && !$this->normalGrille) {
			 $context->buildViolation($message)->atPath('normalGrille')->addViolation();
		}
		if($this->evaluation->getRisque()->isPhysical()==false && !$this->grille) {
			 $context->buildViolation($message)->atPath('grille')->addViolation();
		}
	}


    /**
     * Set maturite
     *
     * @param Maturite $maturite
     * @return EvaluationHasCause
     */
    public function setMaturite(Maturite $maturite = null)
    {
        $this->maturite = $maturite;
        $controles = $this->getEvaluation()->getRisque()->getCauseOfRisque()->filter(
        		function ($entry){
        			return $entry->getCause()->getId() == $this->getCause()->getId();
        		})->first()->getControle();
        foreach ($controles as $ctrl){
        	$ctrl->setMaturiteTheorique($maturite);
        }
        return $this;
    }

    /**
     * Get maturite
     *
     * @return Maturite
     */
    public function getMaturite()
    {
        return $this->maturite;
    }
}
