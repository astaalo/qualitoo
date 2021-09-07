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
class Utilisateur extends User implements UtilisateurInterface
{
	const ROLE_SUPER_ADMIN			= 'ROLE_SUPER_ADMIN';
	const ROLE_ADMIN				= 'ROLE_ADMIN';
	const ROLE_RESPONSABLE 			= 'ROLE_RESPONSABLE';
	const ROLE_RESPONSABLE_ONLY 	= 'ROLE_RESPONSABLE_ONLY';
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
     * @var Societe
     * @ORM\ManyToOne(targetEntity="Societe")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="societe_id", referencedColumnName="id")
     * })
     */
    private $societe;
	
    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Societe", inversedBy="administrateur")
     * @ORM\JoinTable(name="administrateur",
     *   joinColumns={
     *     @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="societe_id", referencedColumnName="id")
     *   }
     * )
     */
    private $societeOfAdministrator;

    /**
     * @ORM\ManyToOne(targetEntity=Profil::class, inversedBy="profil")
     */
    private $profils;

    /**
     * Constructor
     */
    public function __construct()
    {
    	parent::__construct();
        $this->societeOfAdministrator = new ArrayCollection();
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
	 * @return boolean
	 */
	public function hasSocieteOfAdministrator() {
         		return $this->societeOfAdministrator->count() ? true : false;
         	}
	
	/**
	 * @return Collection
	 */
	public function getSocieteOfAdministrator() {
         		return $this->societeOfAdministrator;
         	}
	
	public function setSocieteOfAdministrator($societeOfAdministrator) {
         		if(!$this->societe) {
         			$this->societe = $societeOfAdministrator->count() ? $societeOfAdministrator->get(0) : null;
         		}
         		$this->societeOfAdministrator = $societeOfAdministrator;
         		return $this;
         	}
	
	/**
	 * check if is administrator of this societe
	 * @param integer $societeId
	 * @return boolean
	 */
	public function isAdministratorOf($societeId) {
         		$data = $this->societeOfAdministrator->filter(function($societe) use($societeId) {
         				return $societeId && $societe->getId()==$societeId;
         			});
         		return $data->count() ? true : false;
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
	 * @return ArrayCollection
	 */
	public function getAllSocietes() {
         		$data = new ArrayCollection();
         		$ids = array();
         		$data->add($this->structure->getSociete());
         		$ids[] = $this->structure->getSociete()->getId();
         		foreach($this->societeOfAdministrator as $societe) {
         			if(in_array($societe->getId(), $ids)==false) {
         				array_push($ids, $societe->getId());
         				$data->add($societe);
         			}
         		}
         		return $data;
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
    	} elseif($this->hasSocieteOfAdministrator()) {
    		return "Administrateur";
    	}  elseif($this->manager) {
    		return "Responsable de processus";
    	}  else{
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
    	}  elseif(strtoupper($role)==self::ROLE_ADMIN) {
    		return parent::hasRole(self::ROLE_SUPER_ADMIN) || $this->isAdministratorOf($societeId);
    	}  elseif(strtoupper($role)==self::ROLE_RESPONSABLE) {
    		return $this->manager || $this->isAdministratorOf($societeId) || $this->getSite()->count()>0;
    	} elseif(strtoupper($role)==self::ROLE_USER) {
    		return in_array(strtoupper($role), $this->roles);
    	} else
    		return false;
    }
    
    /**
     * @return array The roles
     */
    public function takeRoles()
    {
    	$roles = array();
		$roles_possibles = array(self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN,self::ROLE_RESPONSABLE,self::ROLE_USER);    
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
     * Add societeOfAdministrator
     *
     * @param Societe $societeOfAdministrator
     * @return Utilisateur
     */
    public function addSocieteOfAdministrator(Societe $societeOfAdministrator)
    {
        $this->societeOfAdministrator[] = $societeOfAdministrator;

        return $this;
    }

    /**
     * Remove societeOfAdministrator
     *
     * @param Societe $societeOfAdministrator
     */
    public function removeSocieteOfAdministrator(Societe $societeOfAdministrator)
    {
        $this->societeOfAdministrator->removeElement($societeOfAdministrator);
    }

    public function getProfils(): ?Profil
    {
        return $this->profils;
    }

    public function setProfils(?Profil $profils): self
    {
        $this->profils = $profils;

        return $this;
    }

}