<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * RisqueHasCause
 *
 * @ORM\Table(name="risque_has_cause")
 * @ORM\Entity(repositoryClass="App\Repository\RisqueHasCauseRepository")
 */
class RisqueHasCause
{
	/**
	 * @var integer
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 */
	private $id;
	
    /**
     * @var Risque
     *
     * @ORM\ManyToOne(targetEntity="Risque",inversedBy="causeOfRisque",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="risque_id", referencedColumnName="id")
     * })
     */
    private $risque;
    

    /**
     * @var Cause
     * @ORM\ManyToOne(targetEntity="Cause",cascade={"persist"})
     * @ORM\JoinColumns({
     * 	@ORM\JoinColumn(name="cause_id", referencedColumnName="id")
     * })
     * 
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
     * @var Cause
     */
    private $newCause;
    
    /**
     * @var Grille
     * @ORM\ManyToOne(targetEntity="Grille")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="grille_id", referencedColumnName="id")
     * })
     */
    private $grille;
    
    /**
     * @var Grille
     */
    public $normalGrille;
    
    /**
     * @var Grille
     */
    public $anormalGrille;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Controle", mappedBy="causeOfRisque", cascade={"persist", "merge", "remove"})
     */
    private $controle;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="PlanAction", mappedBy="causeOfRisque", cascade={"persist", "merge", "remove"})
     */
    private $planAction;
    
    /**
     * @var boolean
     * @ORM\Column(name="transfered", type="boolean", nullable=false)
     *
     */
    private $transfered=false;
    
    public $carto;
    
    
    public function __construct() {
    	$this->controle = new \Doctrine\Common\Collections\ArrayCollection();
    	$this->planAction = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function getId() {
    	return $this->id;
    }
    
    /**
     * @return Risque
     */
	public function getRisque() {
		return $this->risque;
	}
	
	/**
	 * @param Risque $risque
	 * @return RisqueHasCause
	 */
	public function setRisque($risque) {
		$this->risque = $risque;
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
	 * @return RisqueHasCause
	 */
	public function setCause($cause) {
		$this->cause = $cause;
		return $this;
	}

    /**
     * Set modeFonctionnement
     * @param ModeFonctionnement $modeFonctionnement
     * @return RisqueHasCause
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
        return $this->modeFonctionnement;
    }
	
	/**
	 * @return Grille
	 */
	public function getGrille() {
		return $this->grille;
	}
	
	/**
	 * @param Grille $grille
	 * @return RisqueHasCause
	 */
	public function setGrille($grille) {
		$this->grille = $grille;
		return $this;
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
	 * @return RisqueHasCause
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
	 * @return RisqueHasCause
	 */
	public function setAnormalGrille($anormalGrille) {
		$this->anormalGrille = $anormalGrille;
		return $this;
	}
	
	public function getFinalGrille() {
		$grille = $this->grille;
		if($this->risque->isPhysical()) {
			$grille = $this->normalGrille ? $this->normalGrille : ($this->anormalGrille ? $this->anormalGrille : null);
		}
		return $grille;
	}
	
	/**
	 * @return Cause
	 */
	public function getNewCause() {
		return $this->newCause ? $this->newCause : (($this->cause && $this->cause->getEtat()==false) ? $this->cause : null);
	}
	
	/**
	 * set cause
	 * @param Cause $newCause
	 * @return RisqueHasCause
	 */
	public function setNewCause(Cause $newCause) {
		$this->newCause = $newCause;
		$newCause->setEtat(false);
		if($newCause->getLibelle())
			$this->cause = $newCause;
		return $this;
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getControle() {
		return $this->controle;
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getPlanAction() {
		return $this->planAction;
	}
	
	
	/**
	 * @return boolean
	 */
    public function isPhysical() {
    	return $this->getRisque()->isPhysical();
    }
	
	/**
	 * get name of cause
	 * @return string
	 */
	public function __toString() {
		return $this->cause ? $this->cause->getLibelle() : '';
	}
	
	/**
	 * @Assert\Callback(groups={"RisqueValidation", "RisqueIdentification","Default"})
	 */
	public function validate(ExecutionContextInterface $context) {
			$message='Veuillez saisir ou choisir une cause';
		if(!$this->cause && $this->newCause && !$this->newCause->getLibelle()) {
			 $context->buildViolation($message)->atPath('cause')->addViolation();
		}
		$mesageMode='Le mode de fonctionnement est obligatoire';
		if(($this->carto==3 || $this->carto==4 )  && !$this->modeFonctionnement)  {
			$context->buildViolation($mesageMode)->atPath('modeFonctionnement')->addViolation();
		}
	}
	
	/**
	 * get probabilite
	 * @return number
	 */
	public function getProbabilite() {
		$grille = $this->grille ? $this->grille : $this->anormalGrille;
		if($grille==null) {
			return null;
		}
		return $grille->getValeur();
	}

    /**
     * Add controle
     *
     * @param Controle $controle
     * @return RisqueHasCause
     */
    public function addControle(Controle $controle)
    {
    	$controle->setCauseOfRisque($this);
        $this->controle[] = $controle;
    
        return $this;
    }

    /**
     * Remove controle
     *
     * @param Controle $controle
     */
    public function removeControle(Controle $controle)
    {
        $this->controle->removeElement($controle);
    }

    /**
     * Add planAction
     *
     * @param PlanAction $planAction
     * @return RisqueHasCause
     */
    public function addPlanAction(PlanAction $planAction)
    {
    	$planAction->setCauseOfRisque($this);
        $this->planAction[] = $planAction;
    
        return $this;
    }

    /**
     * Remove planAction
     *
     * @param PlanAction $planAction
     */
    public function removePlanAction(PlanAction $planAction)
    {
        $this->planAction->removeElement($planAction);
    }
    

    /**
     * Set transfered
     *
     * @param boolean $transfered
     * @return RisqueHasCause
     */
    public function setTransfered($transfered)
    {
        $this->transfered = $transfered;
        return $this;
    }

    /**
     * Get transfered
     *
     * @return boolean 
     */
    public function getTransfered()
    {
        return $this->transfered;
    }
}
