<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Critere;
use App\Entity\DomaineImpact;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 *
 * @ORM\Table(name="chargement_has_critere")
 * @ORM\Entity()
 */
class CritereChargement {
	
	/**
	 * @var integer
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 */
	private $id;

	 /**
     * @var DomaineImpact
     *
     * @ORM\ManyToOne(targetEntity="DomaineImpact")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="domaine_id", referencedColumnName="id")
     * })
     */
	private $domaine;
	
	/**
     * @var Critere
     *
     * @ORM\ManyToOne(targetEntity="Critere")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="critere_id", referencedColumnName="id")
     * })
     * @Assert\NotNull(message="le critere est obligatoire")
     */
	private $critere;
	
	/**
	 * @var Critere
	 *
	 * @ORM\ManyToOne(targetEntity="Chargement", inversedBy="critere")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="chargement_id", referencedColumnName="id")
	 * })
	 */
	private $chargement;

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
     * Set domaine
     *
     * @param DomaineImpact $domaine
     * @return CritereChargement
     */
    public function setDomaine(DomaineImpact $domaine = null)
    {
        $this->domaine = $domaine;
    
        return $this;
    }

    /**
     * Get domaine
     *
     * @return DomaineImpact
     */
    public function getDomaine()
    {
        return $this->domaine;
    }

    /**
     * Set critere
     *
     * @param Critere $critere
     * @return CritereChargement
     */
    public function setCritere(Critere $critere = null)
    {
        $this->critere = $critere;
    
        return $this;
    }

    /**
     * Get critere
     *
     * @return Critere
     */
    public function getCritere()
    {
        return $this->critere;
    }

    /**
     * Set chargement
     *
     * @param Chargement $chargement
     * @return CritereChargement
     */
    public function setChargement(Chargement $chargement = null)
    {
        $this->chargement = $chargement;
    
        return $this;
    }

    /**
     * Get chargement
     *
     * @return Chargement
     */
    public function getChargement()
    {
        return $this->chargement;
    }
    
    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context) {
    	$message='Veuillez choisir un critere';
    	if(!$this->critere) {
    		$context->buildViolation($message)->atPath('critere')->addViolation();
    	}
    }
}
