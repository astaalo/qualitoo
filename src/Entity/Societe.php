<?php

namespace App\Entity;

use App\Repository\SocieteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\ManyToMany(targetEntity="Utilisateur", mappedBy="societeOfAdministrator", cascade={"persist","remove","merge"})
     */
    protected $administrateur;

    /**
     * @ORM\OneToMany(targetEntity=Processus::class, mappedBy="societe")
     */
    private $processuses;

    public function __construct()
    {
        $this->processuses = new ArrayCollection();
    }
    
    /**
     * Constructor
     */
    
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
	
 /*public function upload() {
    if (null === $this->photo) {
        return;
    }
        $this->photo->move($this->getUploadRootDir(), $this->photo->getClientOriginalName());
        $this->photo = $this->photo->getClientOriginalName();
        $this->photo = null;
    } */  
    
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
     * Get the value of isAdmin
     *
     * @return  boolean
     */ 
    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * @return Collection|Processus[]
     */
    public function getProcessuses(): Collection
    {
        return $this->processuses;
    }

    public function addProcessus(Processus $processus): self
    {
        if (!$this->processuses->contains($processus)) {
            $this->processuses[] = $processus;
            $processus->setSociete($this);
        }

        return $this;
    }

    public function removeProcessus(Processus $processus): self
    {
        if ($this->processuses->removeElement($processus)) {
            // set the owning side to null (unless already changed)
            if ($processus->getSociete() === $this) {
                $processus->setSociete(null);
            }
        }

        return $this;
    }

   
}
