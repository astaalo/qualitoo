<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Evaluation
 * @ORM\Table(name="evaluation")
 * @ORM\Entity(repositoryClass="App\Repository\EvaluationRepository")
 */
class Evaluation
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
     * @ORM\Column(name="date_evaluation", type="datetime", nullable=true)
     */
    private $dateEvaluation;

    /**
     * @var Utilisateur
     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="evaluateur", referencedColumnName="id")
     * })
     */
    private $evaluateur;

    /**
     * @var Utilisateur
     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="validateur", referencedColumnName="id", nullable=true)
     * })
     */
    private $validateur;

    /**
     * @var integer
     *
     * @ORM\Column(name="probabilite", type="integer", nullable=false)
     */
    private $probabilite;

    /**
     * @var integer
     *
     * @ORM\Column(name="gravite", type="integer", nullable=false)
     */
    private $gravite;

    /**
     * @var Criticite
     * @ORM\ManyToOne(targetEntity="Criticite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="criticite_id", referencedColumnName="id")
     * })
     */
    private $criticite;

    /**
     * @var Risque
     * @ORM\ManyToOne(targetEntity="Risque", cascade={"persist", "merge"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="risque_id", referencedColumnName="id")
     * })
     */
    private $risque;

    /**
     * @var Evaluation
     *
     * @ORM\ManyToOne(targetEntity="Evaluation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="precedant", referencedColumnName="id")
     * })
     */
    private $precedant;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="EvaluationHasImpact", orphanRemoval=true, mappedBy="evaluation", cascade={"persist", "merge"})
     */
    private $impactOfEvaluation;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="EvaluationHasCause", orphanRemoval=true, mappedBy="evaluation", cascade={"persist", "merge"})
     */
    private $causeOfEvaluation;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="etat", type="boolean", nullable=true)
     */
    private $etat = true;
    
    /**
     * @var boolean
     * @ORM\Column(name="transfered", type="boolean", nullable=true)
     *
     */
    private $transfered=false;
    
    /**
     * @var integer
     * @ORM\Column(name="annee", type="integer", nullable=true)
     */
    private $annee;
    
    /**
     * @param Risque $risque
     * @return Evaluation
     */
    public function newEvaluation($risque) {
    	$this->setRisque($risque);
    	foreach($this->risque->getCauseOfRisque() as $causeOfRisque) {
    		$causeOfEvaluation = new EvaluationHasCause();
    		$causeOfEvaluation->setCause($causeOfRisque->getCause());
    		$causeOfEvaluation->setGrille($causeOfRisque->getGrille());
    		if($causeOfRisque->getModeFonctionnement()==null) {
    		} elseif($causeOfRisque->getModeFonctionnement()->getId()==1) {
    			$causeOfEvaluation->setNormalGrille($causeOfRisque->getGrille());
    		} elseif($causeOfRisque->getModeFonctionnement()->getId()==2) {
    			$causeOfEvaluation->setAnormalGrille($causeOfRisque->getGrille());
    		}
    		$this->addCauseOfEvaluation($causeOfEvaluation);
    	}
    	
    	/** @var Evaluation $lastEvaluation */
    	$lastEvaluation = $this->risque->getEvaluation()->last();
    	foreach($this->risque->getCartographie()->getDomaine() as $domaine) {
    		$impactOfEvaluation = new EvaluationHasImpact();
    		$impactOfEvaluation->setDomaine($domaine);
    		$impactOfEvaluation->setImpact(new Impact());
    		if($lastEvaluation == null) {
    		} elseif(null != $impactOfRisque = $lastEvaluation->getIOEByDomaine($domaine)) {
    			$impactOfEvaluation->getImpact()->setCritere($impactOfRisque->getImpact()->getCritere());
    			$impactOfEvaluation->setGrille($impactOfRisque->getGrille());
    		} elseif(null != $impactOfRisque = $risque->getImpactOfRisqueByDomaine($domaine)) {
    			$impactOfEvaluation->getImpact()->setCritere($impactOfRisque->getImpact()->getCritere());
    			$impactOfEvaluation->setGrille($impactOfRisque->getGrille());
    		}
    		$this->addImpactOfEvaluation($impactOfEvaluation);
    	}
    	return $this;
    }
    
    /**
     * @return Evaluation
     */
    public function completeDomaine() {
    	$index = 0;
    	$data = new \Doctrine\Common\Collections\ArrayCollection();
    	foreach($this->risque->getCartographie()->getDomaine() as $domaine) {
    		if(null != $impactOfEvaluation = $this->getIOEByDomaine($domaine)) {
    			$impactOfEvaluation->getImpact()->domaine = $domaine;
    			$data->set($index, $impactOfEvaluation);
    			$index++;
    			continue;
    		}
    		$impactOfEvaluation = new EvaluationHasImpact();
    		$impactOfEvaluation->setDomaine($domaine);
    		$impactOfEvaluation->setImpact(new Impact());
    		$data->set($index, $impactOfEvaluation);
    		$index++;
    	}
    	$this->impactOfEvaluation = $data;
    	return $this;
    }
    
    /**
     * @var Processus
     */
    public $processus;
    
    /**
     * @var Structure
     */
    public $structure;
    
    /**
     * @var ProfilRisque
     */
    public $profilRisque;
    
    /**
     * @var Menace
     */
    public $menace;
    
    
    public function __construct() {
    	$this->dateEvaluation = new \DateTime('NOW');
    	$this->annee = $this->dateEvaluation->format('Y');
    	$this->causeOfEvaluation = new \Doctrine\Common\Collections\ArrayCollection();
    	$this->impactOfEvaluation = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    
    /**
     * Get id
     * @return integer
     */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Set id
	 * @return $this
	 */
	public function setId() {
		 $this->id=null;
		 return $this;
	}
	
	/**
	 * Get date Evaluation
	 * @return \DateTime
	 */
	public function getDateEvaluation() {
		return $this->dateEvaluation;
	}
	
	/**
	 * Set date Evaluation
	 * @param \DateTime $dateEvaluation
	 * @return Evaluation
	 */
	public function setDateEvaluation($dateEvaluation) {
		$this->dateEvaluation = $dateEvaluation;
		$this->annee = $dateEvaluation->format('Y');
		return $this;
	}
	
	/**
	 * get evaluateur
	 * @return Utilisateur
	 */
	public function getEvaluateur() {
		return $this->evaluateur;
	}
	
	/**
	 * set evaluateur
	 * @param Utilisateur $evaluateur
	 * @return Evaluation
	 */
	public function setEvaluateur($evaluateur) {
		$this->evaluateur = $evaluateur;
		return $this;
	}
	
	/**
	 * get validateur
	 * @return Utilisateur
	 */
	public function getValidateur() {
		return $this->Validateur;
	}
	
	/**
	 * set validateur
	 * @param Utilisateur $validateur
	 * @return Evaluation
	 */
	public function setValidateur($validateur) {
		$this->validateur = $validateur;
		return $this;
	}
	
	/**
	 * get maturite
	 * @return integer
	 */
	public function getMaturite() {
		return $this->probabilite<3 ? (4 - $this->probabilite) : 1;
	}
	
	/**
	 * Get Probabilite
	 * @return integer
	 */
	public function getProbabilite() {
		return $this->probabilite;
	}
	
	/**
	 * Set Probabilite
	 * @param integer $probabilite
	 * @return Evaluation
	 */
	public function setProbabilite($probabilite) {
		$this->probabilite = $probabilite;
		return $this;
	}
	
	/**
	 * Get Gravite
	 * @return integer
	 */
	public function getGravite() {
		return $this->gravite;
	}
	
	/**
	 * Set Gravite
	 * @param integer $gravite
	 * @return Evaluation
	 */
	public function setGravite($gravite) {
		$this->gravite = $gravite;
		return $this;
	}
	
	/**
	 * Get Criticite
	 * @return integer
	 */
	public function getCriticite() {
		return $this->criticite;
	}
	
	/**
	 * Set Criticite
	 * @param integer $criticite
	 * @return Evaluation
	 */
	public function setCriticite($criticite) {
		$this->criticite = $criticite;
		return $this;
	}
	
	/**
	 * Get Risque
	 * @return Risque
	 */
	public function getRisque() {
		return $this->risque;
	}
	
	/**
	 * Set Risque
	 * @param Risque $risque
	 * @return Evaluation
	 */
	public function setRisque($risque) {
		$this->risque = $risque;
		return $this;
	}
	public function getPrecedant() {
		return $this->precedant;
	}
	
	/**
	 * Set Precedant
	 * @param Evaluation $precedant
	 * @return Evaluation
	 */
	public function setPrecedant($precedant) {
		$this->precedant = $precedant;
		return $this;
	}
	
	/**
	 * Get evaluation's impact
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getImpactOfEvaluation() {
		return $this->impactOfEvaluation;
	}
	
	/**
	 * Get risque's impact
	 * @param EvaluationHasImpact $impactOfEvaluation
	 * @return RisqueHasImpact
	 */
	public function getImpactOfRisque($impactOfEvaluation) {
		$data =  $this->risque->getImpactOfRisque()->filter(function($impactOfRisque) use($impactOfEvaluation) {
				return $impactOfRisque->getImpact()->getId()==$impactOfEvaluation->getImpact()->getId();
			});
		return $data->count() ? $data->first() : null;
	}
	
	/**
	 * Add evaluation's impact
	 * @param EvaluationHasImpact $impactOfEvaluation
	 * @return Evaluation
	 */
	public function addImpactOfEvaluation($impactOfEvaluation) {
		$impactOfEvaluation->setEvaluation($this);
		$this->impactOfEvaluation->add($impactOfEvaluation);
		return $this;
	}
	
	/**
	 * Remove evaluation's impact
	 * @param EvaluationHasImpact $impactOfEvaluation
	 * @return Evaluation
	 */
	public function removeImpactOfEvaluation($impactOfEvaluation) {
		$this->impactOfEvaluation->removeElement($impactOfEvaluation);
		return $this;
	}
	
	/**
	 * @return Evaluation
	 */
	public function cleanUselessImpact() {
		for($index=$this->impactOfEvaluation->count();$index > 0;$index--) {
			$impactOfEvaluation = $this->impactOfEvaluation->get($index - 1);
			if(!$impactOfEvaluation->getImpact() || !$impactOfEvaluation->getImpact()->getCritere() || !$impactOfEvaluation->getImpact()->getGrille()) {
				$this->impactOfEvaluation->remove($index - 1);
			}
		}
		return $this;
	}
	
	/**
	 * @param DomaineSite $domaine
	 * return boolean
	 */
	public function hasDomaine($domaine) {
		$data = $this->impactOfEvaluation->filter(function($impactOfEvaluation) use($domaine) {
				return $impactOfEvaluation->getDomaine() && $impactOfEvaluation->getDomaine()->getId()==$domaine->getId();
			});
		return $data->count() ? true : null;
	}
	
	/**
	 * @param DomaineSite $domaine
	 * return EvaluationHasImpact
	 */
	public function getIOEByDomaine($domaine) {
		$data = $this->impactOfEvaluation->filter(function($impactOfEvaluation) use($domaine) {
				return $impactOfEvaluation->getDomaine() && $impactOfEvaluation->getDomaine()->getId()==$domaine->getId();
			});
		return $data->count() ? $data->first() : null;
	}
	
	/**
	 * Get evaluation's cause
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getCauseOfEvaluation() {
		return $this->causeOfEvaluation;
	}
	
	/**
	 * Get risque's cause
	 * @param EvaluationHasCause $causeOfEvaluation
	 * @return RisqueHasCause
	 */
	public function getCauseOfRisque($causeOfEvaluation) {
		$data =  $this->risque->getCauseOfRisque()->filter(function($causeOfRisque) use($causeOfEvaluation) {
				return $causeOfRisque->getCause()->getId()==$causeOfEvaluation->getCause()->getId();
			});
		return $data->count() ? $data->first() : null;
	}
	
	/**
	 * Add evaluation's cause
	 * @param EvaluationHasCause $causeOfEvaluation
	 * @return Evaluation
	 */
	public function addCauseOfEvaluation($causeOfEvaluation) {
		if($causeOfEvaluation->getCause()){
			$causeOfEvaluation->setEvaluation($this);
			$risk=$this->getRisque();
			$arrRHC = $causeOfEvaluation->getCause()->getRisqueHasCause()->filter(function($rHc) use ($risk) {
					if($rHc->getRisque() == null) { return null; }
					else { return $rHc->getRisque()->getId() == $risk->getId(); }
				});
			if($arrRHC->count() > 0) { 
				$mode = $arrRHC->first()->getModeFonctionnement();
				$causeOfEvaluation->setModeFonctionnement($mode);
				$this->causeOfEvaluation->add($causeOfEvaluation);
			}
		}
		return $this;
	}
	
	/**
	 * Remove evaluation's cause
	 * @param EvaluationHasCause $causeOfEvaluation
	 * @return Evaluation
	 */
	public function removeCauseOfEvaluation($causeOfEvaluation) {
		$this->causeOfEvaluation->removeElement($causeOfEvaluation);
		return $this;
	}
	
	/**
	 * compute evaluation's probabilite
	 * @return Evaluation
	 */
	public function computeProbabilite() {
		$probabilite = 0;
		if($this->getRisque()->isPhysical()) {
			foreach($this->causeOfEvaluation as $causeOfEvaluation) {
				$probabilite += $causeOfEvaluation->getGrille()->getValeur();
			}
			$probabilite = $this->causeOfEvaluation->count() ? round($probabilite / $this->causeOfEvaluation->count()): 0;
		} else {
			foreach($this->causeOfEvaluation as $causeOfEvaluation) {
				$probabilite = $causeOfEvaluation->getGrille() ? max($probabilite, $causeOfEvaluation->getGrille()->getValeur()) : $probabilite;
			}
		}
		if($probabilite) {
			$this->setProbabilite($probabilite);
			
		}
		return $this;
	}
	
	
	/**
	 * compute evaluation's gravite
	 * @return Evaluation
	 */
	public function computeGravite() {
		$gravite = 0;
		foreach($this->impactOfEvaluation as $impactOfEvaluation) {
			$gravite += $impactOfEvaluation->getGrille()->getValeur();
		}
		if($gravite) {
			$this->setGravite(round($gravite / $this->impactOfEvaluation->count()), 0 );
		}
		return $this;
	}
	
	/**
	 * clone a risk
	 * @param Risque $risque
	 * @return Evaluation
	 */
	public function cloneRisque($risque) {
		$this->setRisque($risque);
		foreach($risque->getCauseOfRisque() as $causeOfRisque) {
			$causeOfEvaluation = new EvaluationHasCause();
			$causeOfEvaluation->setCause($causeOfRisque->getCause());
			$causeOfEvaluation->setGrille($causeOfRisque->getGrille());
			$this->addCauseOfEvaluation($causeOfEvaluation);
		}
		foreach($risque->getImpactOfRisque() as $impactOfRisque) {
			$impactOfEvaluation = new EvaluationHasImpact();
			$impactOfEvaluation->setImpact($impactOfRisque->getImpact());
			$impactOfEvaluation->setGrille($impactOfRisque->getGrille());
			$this->addImpactOfEvaluation($impactOfEvaluation);
		}
		$this->setProbabilite($risque->getProbabilite());
		$this->setGravite($risque->getGravite());
		$this->setCriticite($risque->getCriticite());
		if($risque->getEvaluation()->count()) {
			$this->setPrecedant($risque->getEvaluation()->last());
		}
		return $this;
	}
	
	/**
	 * clone a evaluation
	 * @return Evaluation
	 */
	public function cloneEvaluation() {
		foreach($this->getCauseOfEvaluation() as $causeOfEvaluation) {
			$causeOfRisque = $this->getCauseOfRisque($causeOfEvaluation) ? $this->getCauseOfRisque($causeOfEvaluation) : new RisqueHasCause();
			$causeOfRisque->setCause($causeOfEvaluation->getCause());
			$causeOfRisque->setGrille($causeOfEvaluation->getGrille());
			$this->risque->addCauseOfRisque($causeOfRisque);
		}
		$data = new \Doctrine\Common\Collections\ArrayCollection();
		foreach($this->getImpactOfEvaluation() as $impactOfEvaluation) {
			if(null != $impactOfRisque = $this->getImpactOfRisque($impactOfEvaluation)) {
				$impactOfRisque->setImpact($impactOfEvaluation->getImpact());
				$impactOfRisque->setGrille($impactOfEvaluation->getGrille());
			} else {
				$impactOfRisque = new RisqueHasImpact();
				$impactOfRisque->setRisque($this->getRisque());
				$impactOfRisque->setImpact($impactOfEvaluation->getImpact());
				$impactOfRisque->setGrille($impactOfEvaluation->getGrille());
			}
			$data->add($impactOfRisque);
		}
		$this->risque->setAllImpact($data);
		$this->risque->setProbabilite($this->getProbabilite());
		$this->risque->setGravite($this->getGravite());
		$this->risque->setCriticite($this->getCriticite());
		return $this;
	}
	
	/**
	 * @Assert\Callback(groups={"evaluation"})
	 */
	public function validate(ExecutionContextInterface $context) {
		$number = 0;
		foreach($this->impactOfEvaluation as $impactOfRisque) {
			if($impactOfRisque->getImpact() && $impactOfRisque->getImpact()->getCritere() && $impactOfRisque->getGrille()) {
				$number = $number + 1;
			}
		}
		if($number==0) {
			$context->buildViolation("Merci d'évaluer au moins un impact")->atPath('impactOfRisque')->addViolation();
		}
	}



    /**
     * Set etat
     *
     * @param boolean $etat
     * @return Evaluation
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;
    
        return $this;
    }

    /**
     * Get etat
     *
     * @return boolean 
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set transfered
     *
     * @param boolean $transfered
     * @return Evaluation
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

    

    /**
     * Set annee
     *
     * @param integer $annee
     * @return Evaluation
     */
    public function setAnnee($annee)
    {
        $this->annee = $annee;
    
        return $this;
    }

    /**
     * Get annee
     *
     * @return integer 
     */
    public function getAnnee()
    {
        return $this->annee;
    }
    
    /**
     * @return string
     */
    public function showValuesAsToMigrate() {
    	$data = array('causes' => array(), 'impacts' => array());
    	foreach($this->causeOfEvaluation as $cor) {
    		$data['causes'][] = array('id'=>$cor->getCause()->getId(), 'probabilite'=>$cor->getGrille() ? $cor->getGrille()->getNote()->getValeur() : null);
    	}
    	foreach($this->impactOfEvaluation as $ior) {
    		if($ior->getImpact()==null || $ior->getImpact()->getCritere()==null || $ior->getGrille()==null) {
    			continue;
    		}
    		$domaine=$ior->getImpact()->getCritere()->getDomaine();
    		if($domaine && $cor->getGrille()) {
    			$data['impacts'][] = array('id'=>$domaine->getId(), 'gravite'=>$cor->getGrille()->getNote()->getValeur());
    		}
    	}
    	$data['probabilite'] = $this->probabilite;
    	$data['gravite'] = $this->gravite;
    	$data['criticite'] = $this->criticite ? $this->criticite->getNiveau() : null;
    	$data['maturite'] = $this->getMaturite();
    	$data['annee'] = $this->annee;
    	return $data;
    }
}
