<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * RisqueMetier
 * @ORM\Table(name="risque_metier")
 * @ORM\Entity
 */
class RisqueMetier {

	/**
	 * @var integer 
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;
	
	/**
	 * @var Risque
	 * @ORM\OneToOne(targetEntity="Risque", inversedBy="risqueMetier", cascade={"persist"})
	 * @ORM\JoinColumn(name="risque_id", referencedColumnName="id")
	 * @Assert\Valid()
	 */
	private $risque;
	
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
	 * @var Activite
	 * @ORM\ManyToOne(targetEntity="Activite")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="activite_id", referencedColumnName="id")
	 * })
	 */
	private $activite;
	
	/**
	 * @var Structure
	 * @ORM\ManyToOne(targetEntity="Structure")
	 * @ORM\JoinColumns({
	 * 	@ORM\JoinColumn(name="structure_id", referencedColumnName="id")
	 * })
	 */
	private $structure;
	
	/**
	 * @var Structure
	 */
	private $direction;
	
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
	 * get risque
	 * @return Risque
	 */
	public function getRisque() {
		return $this->risque;
	}
	
	/**
	 * set risque
	 * @param Risque $risque
	 * @return RisqueMetier
	 */
	public function setRisque($risque) {
		$risque->setRisqueMetier($this);
		$this->risque = $risque;
		return $this;
	}
	
	/**
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->risque ? $this->risque->__toString() : '';
	}
	

    /**
     * Set activite
     *
     * @param Activite $activite
     * @return RisqueMetier
     */
    public function setActivite(Activite $activite = null)
    {
        $this->activite = $activite;
    
        return $this;
    }

    /**
     * Get activite
     *
     * @return Activite
     */
    public function getActivite()
    {
        return $this->activite;
    }

    /**
     * Set structure
     *
     * @param Structure $structure
     * @return RisqueMetier
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
     * Set processus
     *
     * @param Processus $processus
     * @return RisqueMetier
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
     * get proprietaire
     * @return string
     */
    public function getProprietaire() {
    	return $this->proprietaire;
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
    /**
     * @return Utilisateur
     */
    public function getResponsable() {
    	if($this->activite) {
    		return $this->activite->getProcessus()->getStructure()->getManager();
    	} else {
    		return $this->risque->getIdentification()->getResponsable();
    	}
    }
	public function getDirection() {
		if($this->direction) {
			return $this->direction;
		} elseif($this->structure) {
			return $this->structure->getDirection();
		} else {
			return null;
		}
	}
	
	/**
	 * set direction
	 * @param Structure $direction
	 * @return Risque
	 */
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
// 		if((($this->structure && !$this->structure->getManager()) ||
// 				!$this->structure) && $this->risque->getIdentification()
// 				&& !$this->risque->getIdentification()->getResponsable()) {
// 				 $context->buildViolation('Le responsable du risque est obligatoire')->atPath('proprietaire')->addViolation();
// 			 }
			 if(!$this->activite && $this->risque->getIdentification() && !$this->risque->getIdentification()->getActivite()) {
				 $context->buildViolation("Le nom de l'activité est obligatoire")->atPath('activite')->addViolation();
			 }
	
	}
	
    
}
