<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Absctract\NotificationInterface;
use App\Utils\NotificationUtil;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * RisqueEnvironnemental
 * @ORM\Table(name="risque_environnemental")
 * @ORM\Entity
 */
class RisqueEnvironnemental {

	/**
	 * @var integer 
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;
	
	/**
	 * @var Risque
	 * @ORM\OneToOne(targetEntity="Risque",inversedBy="risqueEnvironnemental")
	 * @ORM\JoinColumn(name="risque_id", referencedColumnName="id")
	 * @Assert\Valid()
	 */
	private $risque;

	
	/**
	 * @var Site
	 * @ORM\ManyToOne(targetEntity="Site")
	 * @ORM\JoinColumns({
	 *    @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=true)
	 * })
	 * @Assert\NotNull(message="Le site est obligatoire", groups={"RisqueValidation", "RisqueIdentification"})
	 */
	private $site;
	

	/**
	 * @var Lieu
	 * @ORM\ManyToOne(targetEntity="Lieu")
	 * @ORM\JoinColumns({
	 *    @ORM\JoinColumn(name="lieu_id", referencedColumnName="id", nullable=true)
	 * })
	 * 
	 * @Assert\NotNull(message="Le lieu est obligatoire", groups={"RisqueValidation"})
	 */
	private $lieu;
	
	/**
	 * @var Manifestation
	 * @ORM\ManyToOne(targetEntity="Manifestation")
	 * @ORM\JoinColumns({
	 *    @ORM\JoinColumn(name="manifestation_id", referencedColumnName="id", nullable=true)
	 * })
	 * 
	 * @Assert\NotNull(message="La manifestation est obligatoire", groups={"RisqueValidation"})
	 */
	private $manifestation;
	
	
	/**
	 * @var string
	 * @ORM\Column(name="proprietaire", type="string", length=100, nullable=true)
	 * 	@Assert\NotNull(message="Le proprietaire est obligatoire", groups={"RisqueValidation"})
	 */
	private $proprietaire;
	
	/**
	 * @var DomaineActivite
	 * @ORM\ManyToOne(targetEntity="DomaineActivite")
	 * @ORM\JoinColumns({
	 *    @ORM\JoinColumn(name="domaine_activite_id", referencedColumnName="id", nullable=true)
	 * })
	 *  @Assert\NotNull(message="Le domaine d\'activité est obligatoire", groups={"RisqueValidation"})
	 */
	private $domaineActivite;
	
	
	/**
	 * @var Equipement
	 * @ORM\ManyToOne(targetEntity="Equipement")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="equipement_id", referencedColumnName="id")
	 * })
	 * 	@Assert\NotNull(message="l\'equipement/acticite est obligatoire", groups={"RisqueValidation"})
	 */
	private $equipement;
	
	
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
     * @return RisqueEnvironnemental
     */
    public function setRisque(Risque $risque = null)
    {
    	$risque->setRisqueEnvironnemental($this);
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
     * Set site
     *
     * @param Site $site
     * @return RisqueEnvironnemental
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;
    
        return $this;
    }

    /**
     * Get site
     *
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }


    /**
     * Set domaineActivite
     *
     * @param DomaineActivite $domaineActivite
     * @return RisqueEnvironnemental
     */
    public function setDomaineActivite(DomaineActivite $domaineActivite = null)
    {
        $this->domaineActivite = $domaineActivite;
    
        return $this;
    }

    /**
     * Get domaineActivite
     *
     * @return DomaineActivite
     */
    public function getDomaineActivite()
    {
        return $this->domaineActivite;
    }

    /**
     * Set proprietaire
     *
     * @param string $proprietaire
     * @return RisqueEnvironnemental
     */
    public function setProprietaire($proprietaire)
    {
        $this->proprietaire = $proprietaire;
    
        return $this;
    }

    /**
     * Get proprietaire
     *
     * @return string 
     */
    public function getProprietaire()
    {
        return $this->proprietaire;
    }



    /**
     * Set equipement
     *
     * @param Equipement $equipement
     * @return RisqueEnvironnemental
     */
    public function setEquipement(Equipement $equipement = null)
    {
        $this->equipement = $equipement;
    
        return $this;
    }

    /**
     * Get equipement
     *
     * @return Equipement
     */
    public function getEquipement()
    {
        return $this->equipement;
    }

    
    /**
     * @Assert\Callback(groups={"RisqueValidation"})
     */
    public function validate(ExecutionContextInterface $context) {
    	if( $this->risque->getIdentification() && !$this->risque->getIdentification()->getActivite()
    			&& !$this->equipement ) {
    				$context->buildViolation("choisir  l'activité ou l'équipement")->atPath('equipement')->addViolation();
    			}
    }

    /**
     * Set lieu
     *
     * @param Lieu $lieu
     * @return RisqueEnvironnemental
     */
    public function setLieu(Lieu $lieu = null)
    {
        $this->lieu = $lieu;
    
        return $this;
    }

    /**
     * Get lieu
     *
     * @return Lieu
     */
    public function getLieu()
    {
        return $this->lieu;
    }

    /**
     * Set manifestation
     *
     * @param Manifestation $manifestation
     * @return RisqueEnvironnemental
     */
    public function setManifestation(Manifestation $manifestation = null)
    {
        $this->manifestation = $manifestation;
    
        return $this;
    }

    /**
     * Get manifestation
     *
     * @return Manifestation
     */
    public function getManifestation()
    {
        return $this->manifestation;
    }
}
