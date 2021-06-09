<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PeriodeAvere
 *
 * @ORM\Table(name="periode_avere")
 * @ORM\Entity()
 */
class PeriodeAvere
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
     * @ORM\Column(name="date_debut", type="date", nullable=true)
     */
    private $dateDebut;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="date_fin", type="date", nullable=true)
     *
     */
    private $dateFin;
    
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="MenaceAvere", mappedBy="periode")
     */
    private $menaces;
    
    /**
     * @var Societe
     * @ORM\ManyToOne(targetEntity="Societe")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="societe_id", referencedColumnName="id")
     * })
     */
    private $societe;
    
    
    /**
     * @var \DateTime
     * @ORM\Column(name="date_saisie", type="date", nullable=true)
     *
     */
    private $dateSaisie;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->menaces = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return PeriodeAvere
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;
    
        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime 
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     * @return PeriodeAvere
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;
    
        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime 
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set societe
     *
     * @param Societe $societe
     * @return PeriodeAvere
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
     * Add menaces
     *
     * @param MenaceAvere $menaces
     * @return PeriodeAvere
     */
    public function addMenace(MenaceAvere $menaces)
    {
        $this->menaces[] = $menaces;
    
        return $this;
    }

    /**
     * Remove menaces
     *
     * @param MenaceAvere $menaces
     */
    public function removeMenace(MenaceAvere $menaces)
    {
        $this->menaces->removeElement($menaces);
    }

    /**
     * Get menaces
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMenaces()
    {
        return $this->menaces;
    }

    /**
     * Set dateSaisie
     *
     * @param \DateTime $dateSaisie
     * @return PeriodeAvere
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
}
