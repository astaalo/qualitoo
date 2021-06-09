<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * AuditHasRisque
 *
 * @ORM\Table(name="audit_has_risque")
 * @ORM\Entity()
 */
class AuditHasRisque
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
     * @ORM\ManyToOne(targetEntity="Risque")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="risque_id", referencedColumnName="id")
     * })
     */
    private $risque;

    /**
     * @var Audit
     * @ORM\ManyToOne(targetEntity="Audit")
     * @ORM\JoinColumns({
     * 	@ORM\JoinColumn(name="audit_id", referencedColumnName="id")
     * })
     */
    private $audit;
    
    
    /**
     * @var \DateTime
     * @ORM\Column(name="date_ajout", type="date", nullable=true)
     *
     */
    private $dateAjout;
    
 /**
     * @var \Maturite
     * @ORM\ManyToOne(targetEntity="Maturite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="maturite_id", referencedColumnName="id")
     * })
     */
    private $maturite;

   public function __construct(){
   		$this->dateAjout = new \DateTime("NOW");
   }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dateAjout
     *
     * @param \DateTime $dateAjout
     * @return AuditHasRisque
     */
    public function setDateAjout($dateAjout)
    {
        $this->dateAjout = $dateAjout;
    
        return $this;
    }

    /**
     * Get dateAjout
     *
     * @return \DateTime 
     */
    public function getDateAjout()
    {
        return $this->dateAjout;
    }

    /**
     * Set risque
     *
     * @param Risque $risque
     * @return AuditHasRisque
     */
    public function setRisque(Risque $risque = null)
    {
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
     * Set audit
     *
     * @param Audit $audit
     * @return AuditHasRisque
     */
    public function setAudit(Audit $audit = null)
    {
        $this->audit = $audit;
    
        return $this;
    }

    /**
     * Get audit
     *
     * @return Audit
     */
    public function getAudit()
    {
        return $this->audit;
    }

    
    /**
     * @Assert\Callback(groups={"audit"})
     */
    public function validate(ExecutionContextInterface $context) {
    	$message='Donner la maturité sil vous plait';
    	if($this->risque && $this->maturite==null) {
    		$context->buildViolation($message)->atPath('maturite')->addViolation();
    	}
    }
    

    /**
     * Set maturite
     *
     * @param Maturite $maturite
     * @return AuditHasRisque
     */
    public function setMaturite(Maturite $maturite = null)
    {
    	$this->risque->setMaturiteReel($maturite);
        $this->maturite = $maturite;
    
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
