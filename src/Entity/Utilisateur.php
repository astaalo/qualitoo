<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Utilisateur
 *
 * @ORM\Table(name="utilisateur")
 * @ORM\Entity(repositoryClass=UtilisateurRepository::class)
 */
class Utilisateur extends User
{
	const ROLE_SUPER_ADMIN			= 'ROLE_SUPER_ADMIN';
	const ROLE_ADMIN				= 'ROLE_ADMIN';
	const ROLE_USER 				= 'ROLE_USER';
	
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected  $id;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=100, nullable=false)
     * @Assert\NotNull(message="Entrez un prénom s'il vous plait")
     */
    protected $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=100, nullable=false)
     * @Assert\NotNull(message="Entrez un nom s'il vous plait")
     */
    protected $nom;
    
    /**
     * @var integer
     * @ORM\Column(name="matricule", type="integer", nullable=true)
     */
    protected $matricule;
    
    /**
     * @ORM\Column(name="telephone", type="string", length=25, nullable=true)
     */
    protected $telephone;
    
    /**
     * var string
     */
    protected $profil;
    
    /**
     * @ORM\Column(name="etat", type="integer", length=1)
     */
    protected $etat = 1;

    /**
     * @ORM\Column(name="manager", type="boolean")
     */
    protected $manager = false;
    
    /**
     * @ORM\Column(name="connectWindows", type="boolean")
     */
    protected $connectWindows = true;
    
    /**
     * @var Structure
     *
     * @ORM\ManyToOne(targetEntity="Structure", inversedBy="utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="structure_id", referencedColumnName="id")
     * })
     *  @Assert\NotNull(message="Veuillez choisir la structure s'il vous plait")
     */
    private $structure;
    
    /**
     * @var Site
     *
     * @ORM\OneToMany(targetEntity="Site", mappedBy="responsable")
     */
    private $site;
    
    
    /**
     * @var Societe
     * @ORM\ManyToOne(targetEntity="Societe")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="societe_id", referencedColumnName="id")
     * })
     */
    private $societe;
   

    /**
     * Constructor
     */
    public function __construct()
    {
    	parent::__construct();
        $this->societeOfAdministrator = new ArrayCollection();
        $this->societeOfRiskManager = new ArrayCollection();
        $this->societeOfAuditor = new ArrayCollection();
        $this->structureOfConsulteur = new ArrayCollection();
    }
    
    /**
     * @return string
     */
	public function getPrenom() {
		return $this->prenom;
	}
	
	public function setPrenom($prenom) {
		$this->prenom = $prenom;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getNom() {
		return $this->nom;
	}
	
	public function setNom( $nom) {
		$this->nom = $nom;
		return $this;
	}
	
	/**
	 * @return Structure
	 */
	public function getStructure() {
		return $this->structure;
	}
	
	public function setStructure($structure) {
		$this->structure = $structure;
		return $this;
	}
	
	
	/**
	 * @return integer
	 */
	public function getMatricule() {
		return $this->matricule;
	}
	
	public function setMatricule($matricule) {
		$this->matricule = $matricule;
		return $this;
	}
	
	/**
	 * @var string
	 */
	public function getTelephone() {
		return $this->telephone;
	}
	
	public function setTelephone($telephone) {
		$this->telephone = $telephone;
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function isManager() {
		return $this->manager ? true : false;
	}
	
	public function setManager($manager) {
		$this->manager = $manager;
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function isConnectWindows() {
		return $this->connectWindows ? true : false;
	}
	
	public function setConnectWindows($ConnectWindows) {
		$this->connectWindows = $ConnectWindows;
		return $this;
	}
	
	/**
	 * set ssociete
	 * @param Societe $societe
	 * @return Utilisateur
	 */
	public function setSociete($societe) {
		$this->societe = $societe;
		return $this;
	}
	
	/**
	 * get societe
	 * @return Societe
	 */
	public function getSociete() {
		return $this->societe;
	}
	
	
	
	/**
     * Get libelle
     *
     * @return string
     */
    public function __toString()
    {
    	return $this->prenom.' '.$this->nom;
    }
	
    /**
     * Get profil
     *
     * @return string
     */
    public function getProfil()
    {
    	if($this->hasRole('ROLE_ADMIN')) {
    		return 'Super Administrateur';
    	}else{
    		return "Simple utilisateur"; 
    	}
    }
	
    /**
     * (non-PHPdoc)
     * @see \FOS\UserBundle\Model\User::hasRole()
     */
    public function hasRole($role, $societeId = null) {
    	$societeId = $societeId ? $societeId : ($this->societe ? $this->societe->getId() : null);
    	if(strtoupper($role)==self::ROLE_SUPER_ADMIN) {
    		return parent::hasRole(self::ROLE_ADMIN);
    	}  elseif(strtoupper($role)==self::ROLE_USER) {
    		return in_array(strtoupper($role), $this->roles);
    	}
    		return false;
    }
    
    /**
     * @return array The roles
     */
    public function takeRoles()
    {
    	$roles = array();
		$roles_possibles = array(self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN,self::ROLE_USER);    
    	foreach ($roles_possibles as $role) {
    		if($this->hasRole($role))
    			 $roles[]= $role;
    	}
    	return array_unique($roles);
    }
    
	
    /**
     * @param array $roles
     * @return boolean
     */
    public function hasRoles($roles) {
    	foreach($roles as $role) {
    		if($this->hasRole($role)) {
    			return true;
    		}
    	}
    	return false;
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
     * Set etat
     *
     * @param integer $etat
     * @return Utilisateur
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return integer 
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Get manager
     *
     * @return boolean 
     */
    public function getManager()
    {
        return $this->manager;
    }
    
    /**
     * Get connectWindows
     *
     * @return boolean
     */
    public function getConnectWindows()
    {
    	return $this->connectWindows;
    }

    
    /**
     * Add site
     *
     * @param Site $site
     * @return Utilisateur
     */
    public function addSite(Site $site)
    {
        $this->site[] = $site;
    
        return $this;
    }

    /**
     * Remove site
     *
     * @param Site $site
     */
    public function removeSite(Site $site)
    {
        $this->site->removeElement($site);
    }
    
    /**
     * Get site
     * @return Collection
     */
    public function getSite()
    {
    	return $this->site;
    }
    
    /**
     * Get ids's site
     * @return array
     */
    public function getSiteIds()
    {
    	$data = array();
    	foreach($this->site as $site) {
    		$data[] = $site->getId();
    	}
    	return $data;
    }
    
}