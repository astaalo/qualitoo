<?php

namespace App\Entity;

use App\Repository\SocieteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SocieteRepository::class)
 */
class Societe
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
     *
     * @ORM\Column(name="libelle", type="string", length=100, nullable=false)
     */
    private $libelle;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $photo;
    
    /**
     * @Assert\File(maxSize="6000000")
     */
    public $file;

    /**
     * @var boolean
     *
     * @ORM\Column(name="etat", type="boolean", nullable=true)
     */
    private $etat = true;

      /**
     * @var boolean
     *
     * @ORM\Column(name="isAdmin", type="boolean", nullable=true)
     */
    private $isAdmin;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Colonne", mappedBy="societe")
     */
    private $colonne;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="Famille", inversedBy="societe", cascade={"persist","remove","merge"})
     * @ORM\JoinTable(name="societe_has_famille",
     *   joinColumns={
     *     @ORM\JoinColumn(name="societe_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="famille_id", referencedColumnName="id")
     *   }
     * )
     */
    private $famille;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Utilisateur", mappedBy="societeOfAdministrator", cascade={"persist","remove","merge"})
     */
    protected $administrateur;
    
    /** 
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Utilisateur", mappedBy="societeOfRiskManager", cascade={"persist","remove","merge"})
     */
    protected $riskManager;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Utilisateur", mappedBy="societeOfAuditor", cascade={"persist","remove","merge"})
     */
    protected $auditeur;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Relance", mappedBy="societe")
     */
    private $relances;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->famille = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * @return integer
     */
    public function getId() {
    	return $this->id;
    }
    
    /**
     * @return string
     */
	public function getLibelle() {
		return $this->libelle;
	}
	
	/**
	 * @param string $libelle
	 * @return Societe
	 */
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}
    
	/**
	 * @return boolean
	 */
	public function getEtat() {
		return $this->etat;
	}
	
	/**
	 * @param boolean $etat
	 * @return Societe
	 */
	public function setEtat($etat) {
		$this->etat = $etat;
		return $this;
	}
	
	/**
	 * get colonnes
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getColonne() {
		return $this->colonne;
	}
	
	/**
	 * set colonnes
	 * @param \Doctrine\Common\Collections\ArrayCollection $colonne
	 * @return Societe
	 */
	public function setColonne($colonne) {
		$this->colonne = $colonne;
		return $this;
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getFamille() {
		return $this->famille;
	}
	
	/**
	 * @param \Doctrine\Common\Collections\ArrayCollection $famille
	 * @return Societe
	 */
	public function setFamille($famille) {
		$this->famille = $famille;
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function hasChangeFile(){
	
	}
	
	public function setChangeFile(){
	
	}
	
	/**
	 * @return boolean
	 */
	public function isChangeFile(){
	
	}
    
	/**
	 * @return string
	 */
    public function getAbsolutePath() {
        return null === $this->photo ? null : $this->getUploadRootDir().'/'.$this->photo;
    }

    /**
     * @return string
     */
    public function getWebPath()
    {
    	$path = (null === $this->photo) ? '../../orangemain/images/societe_default_image.png' : $this->photo;
    	return $this->getUploadDir() . '/' . $path;
    }
    
	/**
	 * @return string
	 */    
    protected function getUploadRootDir() {
        return __DIR__.'../../public/'.$this->getUploadDir();
        // return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    /**
     * @return string
     */
    protected function getUploadDir() {
        return 'uploads/photos';
    }
	
    public function upload() {
    	if (null === $this->file) {
    		return;
    	}
    	$this->file->move($this->getUploadRootDir(), $this->file->getClientOriginalName());
    	$this->photo = $this->file->getClientOriginalName();
    	$this->file = null;
    }
    
	/**
	 * Get libelle
	 * @return string
	 */
	public function __toString()
	{
		return $this->libelle;
	}
	
	/**
	 * @return string
	 */
	public function getPhoto() {
		return $this->photo;
	}
	
	/**
	 * @param string $photo
	 * @return Societe
	 */
	public function setPhoto($photo) {
		$this->photo = $photo;
		return $this;
	}
	
	

    /**
     * Add famille
     *
     * @param Famille $famille
     * @return Societe
     */
    public function addFamille(Famille $famille)
    {
        $this->famille[] = $famille;

        return $this;
    }

    /**
     * Remove famille
     *
     * @param Famille $famille
     */
    public function removeFamille(Famille $famille)
    {
        $this->famille->removeElement($famille);
    }

    /**
     * Add administrateur
     *
     * @param Utilisateur $administrateur
     * @return Societe
     */
    public function addAdministrateur(Utilisateur $administrateur)
    {
        $this->administrateur[] = $administrateur;

        return $this;
    }

    /**
     * Remove administrateur
     *
     * @param Utilisateur $administrateur
     */
    public function removeAdministrateur(Utilisateur $administrateur)
    {
        $this->administrateur->removeElement($administrateur);
    }

    /**
     * Get administrateur
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdministrateur()
    {
        return $this->administrateur;
    }

    /**
     * Add riskManager
     *
     * @param Utilisateur $riskManager
     * @return Societe
     */
    public function addRiskManager(Utilisateur $riskManager)
    {
        $this->riskManager[] = $riskManager;

        return $this;
    }

    /**
     * Remove riskManager
     *
     * @param Utilisateur $riskManager
     */
    public function removeRiskManager(Utilisateur $riskManager)
    {
        $this->riskManager->removeElement($riskManager);
    }
	
    /**
     * Get riskManager
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRiskManager()
    {
        return $this->riskManager;
    }
	
    /**
     * Add auditeur
     *
     * @param Utilisateur $auditeur
     * @return Societe
     */
    public function addAuditeur(Utilisateur $auditeur)
    {
        $this->auditeur[] = $auditeur;

        return $this;
    }

    /**
     * Remove auditeur
     *
     * @param Utilisateur $auditeur
     */
    public function removeAuditeur(Utilisateur $auditeur)
    {
        $this->auditeur->removeElement($auditeur);
    }

    /**
     * Get auditeur
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAuditeur()
    {
        return $this->auditeur;
    }

    /**
     * Add colonne
     *
     * @param Colonne $colonne
     * @return Societe
     */
    public function addColonne(Colonne $colonne)
    {
        $this->colonne[] = $colonne;
    
        return $this;
    }

    /**
     * Remove colonne
     *
     * @param Colonne $colonne
     */
    public function removeColonne(Colonne $colonne)
    {
        $this->colonne->removeElement($colonne);
    }

    /**
     * Add relances
     *
     * @param Relance $relances
     * @return Societe
     */
    public function addRelance(Relance $relances)
    {
        $this->relances[] = $relances;
    
        return $this;
    }

    /**
     * Remove relances
     *
     * @param Relance $relances
     */
    public function removeRelance(Relance $relances)
    {
        $this->relances->removeElement($relances);
    }

    /**
     * Get relances
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRelances()
    {
        return $this->relances;
    }
    
    public function getRelance(){
    	return $this->relances->first();
    }

    /**
     * Get the value of isAdmin
     *
     * @return  boolean
     */ 
    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

   
}
