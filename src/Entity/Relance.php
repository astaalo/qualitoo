<?php

namespace App\Entity;

use App\Repository\RelanceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RelanceRepository::class)
 */
class Relance
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Societe
     * @ORM\ManyToOne(targetEntity="Societe", inversedBy="relances")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="societe_id", referencedColumnName="id")
     * })
     */
    private $societe;
    
    /**
	 * @var integer 
	 * @ORM\Column(name="nbDebut", type="integer", nullable=true)
	 */
    private $nbDebut;
    
     /**
     * @var UniteTemps
     * @ORM\ManyToOne(targetEntity="UniteTemps")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ut_debut_id", referencedColumnName="id")
     * })
     */
    private $uniteTpsDebut;
    
    /**
     * @var integer
     * @ORM\Column(name="nbFrequence", type="integer", nullable=true)
     */
    private $nbFrequence;
    
    /**
     * @var UniteTemps
     * @ORM\ManyToOne(targetEntity="UniteTemps")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ut_frequence_id", referencedColumnName="id")
     * })
     */
    private $uniteTpsFrequence;
    
  /**
     * @var Phase
     * @ORM\ManyToOne(targetEntity="Phase")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="phase_id", referencedColumnName="id")
     * })
     */
    private $phase;
    
    /**
     * @var datetime
     *
     * @ORM\Column(name="date_saisie", type="date", nullable=false)
     *
     */
    private $dateCreation;
    
    /**
     * @var boolean
     * @ORM\Column(name="isActif", type="boolean", nullable=false)
     */
    private $isActif;

    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Repetition", mappedBy="risque", cascade={"persist", "merge"})
     */
    private $repetitions;
    

    /**
     * Constructor
     */
    public function __construct() {
    	$this->dateSaisie=new \DateTime("NOW");
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
     * Set nbDebut
     *
     * @param integer $nbDebut
     * @return Relance
     */
    public function setNbDebut($nbDebut)
    {
        $this->nbDebut = $nbDebut;
    
        return $this;
    }

    /**
     * Get nbDebut
     *
     * @return integer 
     */
    public function getNbDebut()
    {
        return $this->nbDebut;
    }

    /**
     * Set nbFrequence
     *
     * @param integer $nbFrequence
     * @return Relance
     */
    public function setNbFrequence($nbFrequence)
    {
        $this->nbFrequence = $nbFrequence;
    
        return $this;
    }

    /**
     * Get nbFrequence
     *
     * @return integer 
     */
    public function getNbFrequence()
    {
        return $this->nbFrequence;
    }
    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Relance
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;
    
        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime 
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set societe
     *
     * @param Societe $societe
     * @return Relance
     */
    public function setSociete(Societe $societe = null)
    {
        $this->societe = $societe;
    
        return $this;
    }

    /**
     * Get societe
     *
     * @return Societe 
     */
    public function getSociete()
    {
        return $this->societe;
    }


    /**
     * Set isActif
     *
     * @param boolean $isActif
     * @return Relance
     */
    public function setIsActif($isActif)
    {
        $this->isActif = $isActif;
    
        return $this;
    }

    /**
     * Get isActif
     *
     * @return boolean 
     */
    public function getIsActif()
    {
        return $this->isActif;
    }

    /**
     * Add repetitions
     *
     * @param Repetition $repetitions
     * @return Relance
     */
    public function addRepetition(Repetition $repetitions)
    {
        $this->repetitions[] = $repetitions;
    
        return $this;
    }

    /**
     * Remove repetitions
     *
     * @param Repetition $repetitions
     */
    public function removeRepetition(Repetition $repetitions)
    {
        $this->repetitions->removeElement($repetitions);
    }

    /**
     * Get repetitions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRepetitions()
    {
        return $this->repetitions;
    }

    /**
     * Set uniteTpsDebut
     *
     * @param UniteTemps $uniteTpsDebut
     * @return Relance
     */
    public function setUniteTpsDebut(UniteTemps $uniteTpsDebut = null)
    {
        $this->uniteTpsDebut = $uniteTpsDebut;
    
        return $this;
    }

    /**
     * Get uniteTpsDebut
     *
     * @return UniteTemps 
     */
    public function getUniteTpsDebut()
    {
        return $this->uniteTpsDebut;
    }

    /**
     * Set uniteTpsFrequence
     *
     * @param UniteTemps $uniteTpsFrequence
     * @return Relance
     */
    public function setUniteTpsFrequence(UniteTemps $uniteTpsFrequence = null)
    {
        $this->uniteTpsFrequence = $uniteTpsFrequence;
    
        return $this;
    }

    /**
     * Get uniteTpsFrequence
     *
     * @return UniteTemps 
     */
    public function getUniteTpsFrequence()
    {
        return $this->uniteTpsFrequence;
    }

    /**
     * Set phase
     *
     * @param Phase $phase
     * @return Relance
     */
    public function setPhase(Phase $phase = null)
    {
        $this->phase = $phase;
    
        return $this;
    }

    /**
     * Get phase
     *
     * @return Phase 
     */
    public function getPhase()
    {
        return $this->phase;
    }
}
