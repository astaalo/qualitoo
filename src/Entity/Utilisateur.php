<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
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
	const ROLE_RISKMANAGER 			= 'ROLE_RISKMANAGER';
	const ROLE_CHEFPROJET 			= 'ROLE_CHEFPROJET';
	const ROLE_RESPONSABLE 			= 'ROLE_RESPONSABLE';
	const ROLE_RESPONSABLE_ONLY 	= 'ROLE_RESPONSABLE_ONLY';
	const ROLE_AUDITEUR 			= 'ROLE_AUDITEUR';
	const ROLE_CONSULTEUR			= 'ROLE_CONSULTEUR';
	const ROLE_SUPERVISEUR 			= 'ROLE_SUPERVISEUR';
	const ROLE_PORTEUR 				= 'ROLE_PORTEUR';
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
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Projet", mappedBy="utilisateur")
     */
    private $projet;
    
    
    /**
     * @var Societe
     * @ORM\ManyToOne(targetEntity="Societe")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="societe_id", referencedColumnName="id")
     * })
     */
    private $societe;
	
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Societe", inversedBy="auditeur")
     * @ORM\JoinTable(name="auditeur",
     *   joinColumns={
     *     @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="societe_id", referencedColumnName="id")
     *   }
     * )
     */
    private $societeOfAuditor;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Structure", inversedBy="consulteur")
     * @ORM\JoinTable(name="consulteur",
     *   joinColumns={ @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id") },
     *   inverseJoinColumns={ @ORM\JoinColumn(name="structure_id", referencedColumnName="id") }
     * )
     */
    private $structureOfConsulteur;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Societe", inversedBy="riskManager")
     * @ORM\JoinTable(name="risk_manager",
     *   joinColumns={
     *     @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="societe_id", referencedColumnName="id")
     *   }
     * )
     */
    private $societeOfRiskManager;
	
    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="PlanAction", mappedBy="porteur")
     */
    private $paOfPorteur;
	
    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="PlanAction", mappedBy="superviseur")
     */
    private $paOfSuperviseur;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Controle", mappedBy="porteur")
     */
    private $controleOfPorteur;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Controle", mappedBy="superviseur")
     */
    private $controleOfSuperviseur;

    /**
     * Constructor
     */
    public function __construct()
    {
    	parent::__construct();
        $this->societeOfAdministrator = new \Doctrine\Common\Collections\ArrayCollection();
        $this->societeOfRiskManager = new \Doctrine\Common\Collections\ArrayCollection();
        $this->societeOfAuditor = new \Doctrine\Common\Collections\ArrayCollection();
        $this->structureOfConsulteur = new \Doctrine\Common\Collections\ArrayCollection();
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
	 * @return \Doctrine\Common\Collections\Collection
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
	 * @return boolean
	 */
	public function hasSocieteOfAuditor() {
		return $this->societeOfAuditor->count() ? true : false;
	}
	
	/**
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getSocieteOfAuditor() {
		return $this->societeOfAuditor;
	}
	
	public function setSocieteOfAuditor($societeOfAuditor) {
		if(!$this->societe) {
			$this->societe = $societeOfAuditor->count() ? $societeOfAuditor->get(0) : null;
		}
		$this->societeOfAuditor = $societeOfAuditor;
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function hasStructureOfConsulteur() {
		return $this->structureOfConsulteur->count() ? true : false;
	}
	
	/**
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getStructureOfConsulteur() {
		return $this->structureOfConsulteur;
	}
	
	public function setStructureOfConsulteur($structureOfConsulteur) {
		if(!$this->structure) {
			$this->structure = $structureOfConsulteur->count() ? $structureOfConsulteur->get(0) : null;
		}
		$this->structureOfConsulteur= $structureOfConsulteur;
		return $this;
	}
	
	/**
	 * check if is auditor of this societe
	 * @param integer $societeId
	 * @return boolean
	 */
	public function isAuditorOf($societeId) {
		$data = $this->societeOfAuditor->filter(function($societe) use($societeId) {
				return $societeId && $societe->getId()==$societeId;
			});
		return $data->count() ? true : false;
	}
	
	/**
	 * check if is consulteur of this structure
	 * @param integer $structureId
	 * @return boolean
	 */
	public function isConsulteurOf($structureId) {
		$data = $this->structureOfConsulteur->filter(function($structure) use($structureId) {
				return $structureId && $structure->getId()==$structureId;
			});
		return $data->count() ? true : false;
	}
	
	/**
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getSocieteOfRiskManager() {
		return $this->societeOfRiskManager;
	}
	
	public function setSocieteOfRiskManager($societeOfRiskManager) {
		if(!$this->societe) {
			$this->societe = $societeOfRiskManager->count() ? $societeOfRiskManager->get(0) : null;
		}
		$this->societeOfRiskManager = $societeOfRiskManager;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function hasSocieteOfRiskManager() {
		return $this->societeOfRiskManager->count() ? true : false;
	}
	
	/**
	 * check if is riskManager of this societe
	 * @param integer $societeId
	 * @return boolean
	 */
	public function isRiskManagerOf($societeId) {
		$data = $this->societeOfRiskManager->filter(function($societe) use($societeId) {
				return $societeId && $societe->getId()==$societeId;
			});
		return $data->count() ? true : false;
	}
	
	/**
	 * @param number $societeId
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getProjetOf($societeId) {
		return $this->projet->filter(function($projet) use($societeId) {
				return $projet->getSociete()->getId()==$societeId;
			});
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
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getAllSocietes() {
		$data = new \Doctrine\Common\Collections\ArrayCollection();
		$ids = array();
		$data->add($this->structure->getSociete());
		$ids[] = $this->structure->getSociete()->getId();
		foreach($this->societeOfAdministrator as $societe) {
			if(in_array($societe->getId(), $ids)==false) {
				array_push($ids, $societe->getId());
				$data->add($societe);
			}
		}
		foreach($this->societeOfRiskManager as $societe) {
			if(in_array($societe->getId(), $ids)==false) {
				array_push($ids, $societe->getId());
				$data->add($societe);
			}
		}
		foreach($this->societeOfAuditor as $societe) {
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
    	} elseif($this->hasSocieteOfAuditor()) {
    		return "Auditeur";
    	}  elseif($this->hasStructureOfConsulteur()) {
    		return "Consulteur de Risques";
    	}  elseif($this->hasSocieteOfRiskManager()) {
    		return "Risk Manager";
    	} elseif($this->manager) {
    		return "Responsable de processus";
    	} elseif($this->controleOfSuperviseur->count() || $this->paOfSuperviseur->count()) {
    		return "Superviseur";
    	} elseif($this->site->count()>0) {
    		return "Responsable de site";
    	} else{
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
    	}  elseif(strtoupper($role)==self::ROLE_RISKMANAGER) {
    		return $this->isRiskManagerOf($societeId)|| $this->isAdministratorOf($societeId);
    	}  elseif(strtoupper($role)==self::ROLE_AUDITEUR) {
    		return $this->isAuditorOf($societeId) || $this->isAdministratorOf($societeId);
    	}  elseif(strtoupper($role)==self::ROLE_PORTEUR) {
    		return true;
    	}  elseif(strtoupper($role)==self::ROLE_SUPERVISEUR) {
    		return $this->paOfSuperviseur->count() || $this->controleOfSuperviseur->count() || $this->isAdministratorOf($societeId);
    	} elseif(strtoupper($role)==self::ROLE_RESPONSABLE) {
    		return $this->manager || $this->isAdministratorOf($societeId) || $this->getSite()->count()>0;
    	} elseif(strtoupper($role)==self::ROLE_CHEFPROJET) {
    		return $this->getProjetOf($societeId)->count()>0;
    	} elseif(strtoupper($role)==self::ROLE_USER) {
    		return in_array(strtoupper($role), $this->roles);
    	} elseif(strtoupper($role)==self::ROLE_RESPONSABLE_ONLY){
    		return ($this->manager || $this->getSite()->count()>0 ) &&
    				! (parent::hasRole(self::ROLE_SUPER_ADMIN) || $this->isAdministratorOf($societeId)) &&
    				! ($this->isAuditorOf($societeId) || $this->isAdministratorOf($societeId)) &&
    				! ( $this->isRiskManagerOf($societeId)|| $this->isAdministratorOf($societeId));
    	}else
    		return false;
    }
    
    /**
     * @return array The roles
     */
    public function takeRoles()
    {
    	$roles = array();
		$roles_possibles = array(self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN,self::ROLE_RISKMANAGER,self::ROLE_AUDITEUR ,self::ROLE_PORTEUR,self::ROLE_SUPERVISEUR,self::ROLE_RESPONSABLE,self::ROLE_USER);    
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

    /**
     * Add societeOfAuditor
     *
     * @param Societe $societeOfAuditor
     * @return Utilisateur
     */
    public function addSocieteOfAuditor(Societe $societeOfAuditor)
    {
        $this->societeOfAuditor[] = $societeOfAuditor;

        return $this;
    }

    /**
     * Remove societeOfAuditor
     *
     * @param Societe $societeOfAuditor
     */
    public function removeSocieteOfAuditor(Societe $societeOfAuditor)
    {
        $this->societeOfAuditor->removeElement($societeOfAuditor);
    }

    /**
     * Add societeOfRiskManager
     *
     * @param Societe $societeOfRiskManager
     * @return Utilisateur
     */
    public function addSocieteOfRiskManager(Societe $societeOfRiskManager)
    {
        $this->societeOfRiskManager[] = $societeOfRiskManager;

        return $this;
    }

    /**
     * Remove societeOfRiskManager
     *
     * @param Societe $societeOfRiskManager
     */
    public function removeSocieteOfRiskManager(Societe $societeOfRiskManager)
    {
        $this->societeOfRiskManager->removeElement($societeOfRiskManager);
    }

    /**
     * Add paOfPorteur
     *
     * @param PlanAction $paOfPorteur
     * @return Utilisateur
     */
    public function addPaOfPorteur(PlanAction $paOfPorteur)
    {
        $this->paOfPorteur[] = $paOfPorteur;

        return $this;
    }

    /**
     * Remove paOfPorteur
     *
     * @param PlanAction $paOfPorteur
     */
    public function removePaOfPorteur(PlanAction $paOfPorteur)
    {
        $this->paOfPorteur->removeElement($paOfPorteur);
    }

    /**
     * Get paOfPorteur
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPaOfPorteur()
    {
        return $this->paOfPorteur;
    }

    /**
     * Add paOfSuperviseur
     *
     * @param PlanAction $paOfSuperviseur
     * @return Utilisateur
     */
    public function addPaOfSuperviseur(PlanAction $paOfSuperviseur)
    {
        $this->paOfSuperviseur[] = $paOfSuperviseur;

        return $this;
    }

    /**
     * Remove paOfSuperviseur
     *
     * @param PlanAction $paOfSuperviseur
     */
    public function removePaOfSuperviseur(PlanAction $paOfSuperviseur)
    {
        $this->paOfSuperviseur->removeElement($paOfSuperviseur);
    }

    /**
     * Get paOfSuperviseur
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPaOfSuperviseur()
    {
        return $this->paOfSuperviseur;
    }

    /**
     * Add controleOfPorteur
     *
     * @param Controle $controleOfPorteur
     * @return Utilisateur
     */
    public function addControleOfPorteur(Controle $controleOfPorteur)
    {
        $this->controleOfPorteur[] = $controleOfPorteur;

        return $this;
    }

    /**
     * Remove controleOfPorteur
     *
     * @param Controle $controleOfPorteur
     */
    public function removeControleOfPorteur(Controle $controleOfPorteur)
    {
        $this->controleOfPorteur->removeElement($controleOfPorteur);
    }

    /**
     * Get controleOfPorteur
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getControleOfPorteur()
    {
        return $this->controleOfPorteur;
    }

    /**
     * Add controleOfSuperviseur
     *
     * @param Controle $controleOfSuperviseur
     * @return Utilisateur
     */
    public function addControleOfSuperviseur(Controle $controleOfSuperviseur)
    {
        $this->controleOfSuperviseur[] = $controleOfSuperviseur;

        return $this;
    }

    /**
     * Remove controleOfSuperviseur
     *
     * @param Controle $controleOfSuperviseur
     */
    public function removeControleOfSuperviseur(Controle $controleOfSuperviseur)
    {
        $this->controleOfSuperviseur->removeElement($controleOfSuperviseur);
    }

    /**
     * Get controleOfSuperviseur
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getControleOfSuperviseur()
    {
        return $this->controleOfSuperviseur;
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
     * @return \Doctrine\Common\Collections\Collection
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
    
    /**
     * Get projet
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProjet()
    {
    	return $this->site;
    }
    
    /**
     * Get ids's projet
     * @return array
     */
    public function getProjetIds()
    {
    	$data = array();
    	foreach($this->projet as $projet) {
    		$data[] = $projet->getId();
    	}
    	return $data;
    }

    /**
     * Add projet
     *
     * @param Projet $projet
     *
     * @return Utilisateur
     */
    public function addProjet(Projet $projet)
    {
        $this->projet[] = $projet;

        return $this;
    }

    /**
     * Remove projet
     *
     * @param Projet $projet
     */
    public function removeProjet(Projet $projet)
    {
        $this->projet->removeElement($projet);
    }
}