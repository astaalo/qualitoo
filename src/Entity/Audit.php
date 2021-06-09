<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Audit
 *
 * @ORM\Table(name="audit")
 * @ORM\Entity(repositoryClass="App\Repository\AuditRepository")
 */
class Audit
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
	 * @var string 
	 * @ORM\Column(name="libelle", type="string", length=100, nullable=false)
	 * @Assert\NotNull(message="Merci de donner une description à votre test", groups={"audit"})
	 * 
	 */
	private $libelle;
	

	/**
	 * @var Utilisateur
	 * @ORM\ManyToOne(targetEntity="Utilisateur")
	 * @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="auteur_id", referencedColumnName="id")
	 * })
	 */
	private $auteur;
	
	/**
	 * @var \DateTime
	 * @ORM\Column(name="date_saisie", type="date", nullable=true)
	 * 
	 */
	private $dateSaisie;
	

	/**
	 * @var ArrayCollection
	 * @ORM\OneToMany(targetEntity="AuditHasRisque", mappedBy="audit",cascade={"persist","merge"})
	 * @Assert\Valid
	 * @Assert\NotNull(groups={"audit"})
	 */
	private $risques;
	
	
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->risques = new ArrayCollection();
        $this->dateSaisie = new \DateTime("NOW");
    }
    
    /**
     * toString
     */
    public function __toString()
    {
    	return 'test';
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
     * Set libelle
     *
     * @param string $libelle
     * @return Audit
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    
        return $this;
    }

    /**
     * Get libelle
     *
     * @return string 
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set dateSaisie
     *
     * @param \DateTime $dateSaisie
     * @return Audit
     */
    public function setDateSaisie($dateSaisie)
    {
        $this->dateSaisie = $dateSaisie;
    
        return $this;
    }

    /**
     * Get dateSaisie
     *
     * @return \DateTime 
     */
    public function getDateSaisie()
    {
        return $this->dateSaisie;
    }

    /**
     * Set auteur
     *
     * @param Utilisateur $auteur
     * @return Audit
     */
    public function setAuteur(Utilisateur $auteur = null)
    {
        $this->auteur = $auteur;
    
        return $this;
    }

    /**
     * Get auteur
     *
     * @return Utilisateur
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Add risques
     *
     * @param AuditHasRisque $risques
     * @return Audit
     */
    public function addRisque(AuditHasRisque $risques)
    {
    	$risques->setAudit($this);
        $this->risques[] = $risques;
    
        return $this;
    }

    /**
     * Remove risques
     *
     * @param AuditHasRisque $risques
     */
    public function removeRisque(AuditHasRisque $risques)
    {
        $this->risques->removeElement($risques);
    }

    /**
     * Get risques
     *
     * @return Collection
     */
    public function getRisques()
    {
        return $this->risques;
    }
}
