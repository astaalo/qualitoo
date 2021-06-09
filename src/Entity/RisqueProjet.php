<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * RisqueProjet
 * @ORM\Table(name="risque_projet")
 * @ORM\Entity
 */
class RisqueProjet {

	/**
	 * @var integer 
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;
	
	/**
	 * @var Risque
	 * @ORM\OneToOne(targetEntity="Risque", cascade={"persist"})
	 * @ORM\JoinColumn(name="risque_id", referencedColumnName="id")
	 * @Assert\Valid()
	 */
	private $risque;
	
	/**
	 * @var \Projet
	 * @ORM\ManyToOne(targetEntity="Projet")
	 * @ORM\JoinColumns({
	 *    @ORM\JoinColumn(name="projet_id", referencedColumnName="id", nullable=true)
	 * })
	 */
	private $projet;
	
	/**
	 * @var \Processus
	 * @ORM\ManyToOne(targetEntity="Processus", cascade={"persist", "merge"})
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="processus_id", referencedColumnName="id")
	 * })
	 * @Assert\NotNull(message="Le nom du processus est obligatoire", groups={"RisqueValidation"})
	 */
	private $processus;
	
	/**
	 * @var Structure
	 */
	private $direction;
	
	/**
	 * @var \Structure
	 * @ORM\ManyToOne(targetEntity="Structure")
	 * @ORM\JoinColumns({
	 * 	@ORM\JoinColumn(name="structure_id", referencedColumnName="id")
	 * })
	 */
	private $structure;
	
	/**
	 * @var string
	 */
	private $proprietaire;
	
	
	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->risque ? $this->risque->__toString() : '';
	}
	

    /**
     * Set risque
     *
     * @param Risque $risque
     * @return RisqueProjet
     */
    public function setRisque(Risque $risque = null)
    {
    	$risque->setRisqueProjet($this);
        $this->risque = $risque;
        return $this;
    }

    /**
     * Get risque
     *
     * @return Risque
     */
    public function getRisque()
    {
        return $this->risque;
    }

    /**
     * Set projet
     *
     * @param Projet $projet
     * @return RisqueProjet
     */
    public function setProjet(Projet $projet = null)
    {
        $this->projet = $projet;
        if($projet) {
        	$this->processus = $projet->getProcessus();
        	if($projet->getProcessus()) {
        		$this->structure = $projet->getProcessus()->getStructure();
        	}
        }
        return $this;
    }

    /**
     * Get projet
     *
     * @return Projet
     */
    public function getProjet()
    {
        return $this->projet;
    }

    /**
     * Set processus
     *
     * @param Processus $processus
     * @return RisqueProjet
     */
    public function setProcessus(Processus $processus = null)
    {
        $this->processus = $processus;
    
        return $this;
    }

    /**
     * Get processus
     *
     * @return Processus
     */
    public function getProcessus()
    {
        return $this->processus;
    }

    /**
     * Set structure
     *
     * @param Structure $structure
     * @return RisqueProjet
     */
    public function setStructure(Structure $structure = null)
    {
        $this->structure = $structure;
    
        return $this;
    }

    /**
     * Get structure
     *
     * @return Structure
     */
    public function getStructure()
    {
        return $this->structure;
    }
    /**
     * get proprietaire
     * @return string
     */
    public function getProprietaire() {
    	return $this->proprietaire;
    }
    
    /**
     * @return Utilisateur
     */
    public function getResponsable() {
    	if($this->proprietaire) {
    		return $this->proprietaire;
    	} else {
    		return $this->risque->getIdentification()?$this->risque->getIdentification()->getResponsable():null;
    	}
    }
    
    /**
     * set proprietaire
     * @param string $proprietaire
     * @return Risque
     */
    public function setProprietaire($proprietaire) {
    	$this->proprietaire = $proprietaire;
    	return $this;
    }
    public function getDirection() {
    	if($this->structure)
			return $this->structure->getDirection();
		else 
			return $this->direction;
    }
    public function setDirection(Structure $direction) {
    	$this->direction = $direction;
    	return $this;
    }
    /**
     * @Assert\Callback(groups={"RisqueValidation"})
     */
    public function validate(ExecutionContextInterface $context) {
    	if(!$this->structure && $this->risque->getIdentification() && !$this->risque->getIdentification()->getStructure()) {
    		$context->buildViolation('Le nom de la structure est obligatoire')->atPath('structure')->addViolation();
    	}
//     	if((($this->structure && !$this->structure->getManager()) ||
//     			!$this->structure) && $this->risque->getIdentification()
//     			&& !$this->risque->getIdentification()->getResponsable()) {
//     				$context->buildViolation('Le responsable du risque est obligatoire')->atPath('structure')->addViolation();
//     			}
    			if(!$this->projet && $this->risque->getIdentification() && !$this->risque->getIdentification()->getProjet()) {
    				$context->buildViolation("Le nom du projet est obligatoire")->atPath('projet')->addViolation();
    			}
    
    }
    /**
     * 
     * @param unknown $entity
     * @param unknown $user
     * @param unknown $carto
     * @return RisqueMetier
     */
    public function transfertToRisqueMetier($entity,$user,$carto){
    	$risk =clone $this->getRisque();
    	$risk->setId(null);
    	$risk->setCartographie($carto);
    	$riskMetier = new RisqueMetier();
    	$riskMetier->setRisque($risk);
    	$riskMetier->getRisque()->setDateTransfert(new \DateTime("NOW"));
    	$riskMetier->getRisque()->setActeurTransfert($user);
    	$riskMetier->getRisque()->setCode("");
    	$riskMetier->getRisque()->setNumero("");
    	$riskMetier->getRisque()->setOrigine($this->getRisque());
    	$riskMetier->setStructure($entity->getStructure());
    	$riskMetier->setProcessus($entity->getProcessus());
    	$riskMetier->setActivite($entity->getActivite());
    	foreach ($this->getRisque()->getCauseOfRisque() as $key =>$cor){
    		$causeOfRisque=new RisqueHasCause();
    		$causeOfRisque->setCause($cor->getCause());
    		$causeOfRisque->setRisque($riskMetier->getRisque());
    		foreach ($cor->getPlanAction() as $key=>$pa){
    			if($pa->getStatut()->getId()<=3){
	    			$pa=clone $pa;
	    			$pa->setId(null);
	    			$causeOfRisque->addPlanAction($pa);
    			}
    		}
    		foreach ($cor->getControle() as $key=>$ctrl){
    				$ctrl=clone $ctrl;
    				$ctrl->setId(null);
    				$causeOfRisque->addControle($ctrl);
    		}
    		$riskMetier->getRisque()->addCauseOfRisque($causeOfRisque);
    	}
    	
    	return $riskMetier;
    }
}
