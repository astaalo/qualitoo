<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\CritereChargement;

/**
 * @ORM\Table(name="chargement")
 * @ORM\Entity
 */
class Chargement{
	
	/**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
	private $id;
	
	/**
	 * @var string
	 * @ORM\Column(name="libelle", type="string", nullable=false)
	 *  @Assert\NotNull(message="Veuillez saisir un libelle s'il vous plait")
	 */
	private $libelle;
	
	/**
	 * @var Utilisateur
	 * @ORM\ManyToOne(targetEntity="Utilisateur")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
	 * })
	 */
	private $utilisateur;
	
	/**
	 * @var Structure
	 * @ORM\ManyToOne(targetEntity="Structure")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="structure_id", referencedColumnName="id" , nullable=true)
	 * })
	 * @Assert\NotNull(message="Veuillez choisir la direction s'il vous plait", groups={"ValideMetier"})
	 */
	private $direction;
	
	/**
	 * @var Projet
	 * @ORM\ManyToOne(targetEntity="Projet")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="projet_id", referencedColumnName="id" , nullable=true)
	 * })
	 * @Assert\NotNull(message="Veuillez choisir le projet s'il vous plait", groups={"ValideProjet"})
	 */
	private $projet;
	
	/**
	 * @var Activite
	 * @ORM\ManyToOne(targetEntity="Activite")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="activite_id", referencedColumnName="id" , nullable=true)
	 * })
	 */
	private $activite;
	
	/**
	 * @var Cartographie
	 * @ORM\ManyToOne(targetEntity="Cartographie")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="cartographie_id", referencedColumnName="id")
	 * })
	 */
	private $cartographie;
	
	/**
	 * @var \DateTime 
	 * @ORM\Column(name="date", type="datetime", nullable=true)
	 */
	private $date;
	
	 /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="Risque", inversedBy="chargement", cascade={"persist", "merge", "remove"})
     * @ORM\JoinTable(name="chargement_has_risque",
     *   joinColumns={
     *     @ORM\JoinColumn(name="chargement_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="risque_id", referencedColumnName="id")
     *   }
     * )
     */
	private $risque;
	
	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @ORM\OneToMany(targetEntity="CritereChargement", mappedBy="chargement",cascade={"persist","merge","remove"})
	 * @Assert\Valid
	 */
	private $critere;
	
	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @ORM\OneToMany(targetEntity="Rapport", mappedBy="chargement",cascade={"persist", "merge","remove"})
	 * @Assert\Valid
	 */
	private $rapport;
	
	/**
	 * @var integer
	 * @ORM\Column(name="etat", type="integer", nullable=true)
	 */
	private $etat;
	
    /**
     * Constructor
     */
    public function __construct()
    {
    	$this->risque = new \Doctrine\Common\Collections\ArrayCollection();
    	$this->rapport = new \Doctrine\Common\Collections\ArrayCollection();
        $this->date =new \DateTime('now');
        $this->critere = new \Doctrine\Common\Collections\ArrayCollection();
        $this->newCritere = new \Doctrine\Common\Collections\ArrayCollection();
        $this->etat=0;
    }
    
    /**
     * @return Chargement
     */
    public function generateRapport() {
    	$numberRisque = $numberCause = $numberPA = $numberControle = $numberImpact = 0;
    	$descriptionRisque = array('probabilite' => 0, 'gravite' => 0, 'criticite' => 0);
    	$descriptionImpact = array();
    	foreach($this->getCritere() as $critere) {
    		$descriptionImpact[$critere->getDomaine()->getId()] = 0;
    	}
    	foreach($this->getRisque() as $risque) {
    		$numberRisque += 1;
    		$numberControle += $risque->getControle()->count();
    		$numberCause 	+= $risque->getCauseOfRisque()->count();
    		$numberPA		+= $risque->getPlanAction()->count();
    		$numberImpact	+= $risque->getImpactOfRisque()->count();
    		$descriptionRisque = array(
    			'probabilite' 	=> $descriptionRisque['probabilite'] + ($risque->getProbabilite() ? 1 : 0), 
    			'gravite' 		=> $descriptionRisque['gravite'] + ($risque->getGravite() ? 1 : 0), 
    			'criticite' 	=> $descriptionRisque['criticite'] + ($risque->getCriticite() ? 1 : 0)
    		);
    		foreach($risque->getImpactOfRisque() as $impactOfRisque) {
    			$descriptionImpact[$impactOfRisque->getDomaine()->getId()] += $impactOfRisque->getGrille() ? 1 : 0;
    		}
    	}
    	$this->addRapport(Rapport::newInstance(Rapport::$types['risque'], $numberRisque, json_encode($descriptionRisque)));
    	$this->addRapport(Rapport::newInstance(Rapport::$types['cause'], $numberCause, null));
    	$this->addRapport(Rapport::newInstance(Rapport::$types['planAction'], $numberPA, null));
    	$this->addRapport(Rapport::newInstance(Rapport::$types['controle'], $numberControle, null));
    	$this->addRapport(Rapport::newInstance(Rapport::$types['impact'], $numberImpact, json_encode($descriptionImpact)));
    	return $this;
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
     * Set date
     *
     * @param \DateTime $date
     * @return Chargement
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set utilisateur
     *
     * @param Utilisateur $utilisateur
     * @return Chargement
     */
    public function setUtilisateur(Utilisateur $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;
    
        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return Utilisateur
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Set direction
     * @param Structure $direction
     * @return Chargement
     */
    public function setDirection(Structure $direction = null)
    {
        $this->direction = $direction;
        return $this;
    }

    /**
     * Get direction
     *
     * @return Structure
     */
    public function getDirection()
    {
        return $this->direction;
    }
    
    /**
     * Set activite
     * @param Activite $activite
     * @return Chargement
     */
    public function setActivite($activite= null) {
    	$this->activite = $activite;
    	return $this;
    }
    
    /**
     * Get activite
     * @return Activite
     */
    public function getActivite() {
    	return $this->activite;
    }

    /**
     * Set projet
     * @param Projet $projet
     * @return Chargement
     */
    public function setProjet($projet = null)
    {
        $this->projet = $projet;
        return $this;
    }

    /**
     * Get projet
     * @return Projet
     */
    public function getProjet()
    {
        return $this->projet;
    }

    /**
     * Set cartographie
     *
     * @param Cartographie $cartographie
     * @return Chargement
     */
    public function setCartographie(Cartographie $cartographie = null)
    {
        $this->cartographie = $cartographie;
    
        return $this;
    }

    /**
     * Get cartographie
     *
     * @return Cartographie
     */
    public function getCartographie()
    {
        return $this->cartographie;
    }

    /**
     * Add risque
     *
     * @param Risque $risque
     * @return Chargement
     */
    public function addRisque(Risque $risque)
    {
        $this->risque[] = $risque;
    
        return $this;
    }

    /**
     * Remove risque
     *
     * @param Risque $risque
     */
    public function removeRisque(Risque $risque)
    {
        $this->risque->removeElement($risque);
    }

    /**
     * Get risque
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRisque()
    {
        return $this->risque;
    }

    /**
     * Set macroProcessus
     *
     * @param Processus $macroProcessus
     * @return Chargement
     */
    public function setMacroProcessus(Processus $macroProcessus = null)
    {
        $this->macroProcessus = $macroProcessus;
    
        return $this;
    }

    /**
     * Get macroProcessus
     *
     * @return Processus
     */
    public function getMacroProcessus()
    {
        return $this->macroProcessus;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     * @return Chargement
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
     * Add critere
     *
     * @return Chargement
     */
    public function addNewCritere($critere)
    {
    	$this->newCritere[] = $critere;
    
    	return $this;
    }
    
    /**
     * Remove critere
     *
     * @param Critere $critere
     */
    public function removeNewCritere(Critere $critere)
    {
    	$this->newCritere->removeElement($critere);
    }
    
    /**
     * Get critere
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNewCritere()
    {
    	return $this->newCritere;
    }
    
    public function loadCritere(){
    	/**
    	 * @var CritereChargement $nc
    	 */
    	foreach ($this->newCritere as $nc){
    		$this->addCritere($nc->getCritere());
    	}
    	
    }

    /**
     * Set etat
     *
     * @param integer $etat
     * @return Chargement
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
     * Add critere
     *
     * @param CritereChargement $critere
     * @return Chargement
     */
    public function addCritere(CritereChargement $critere)
    {
        $this->critere[] = $critere;
    
        return $this;
    }

    /**
     * Remove critere
     *
     * @param CritereChargement $critere
     */
    public function removeCritere(CritereChargement $critere)
    {
        $this->critere->removeElement($critere);
    }

    /**
     * Get critere
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCritere()
    {
        return $this->critere;
    }
    
    /**
     * Add rapport
     * @param Rapport $rapport
     * @return Chargement
     */
    public function addRapport(Rapport $rapport)
    {
    	$rapport->setChargement($this);
    	$this->rapport[] = $rapport;
    	return $this;
    }
    
    /**
     * Remove rapport
     * @param Rapport $rapport
     */
    public function removeRapport(Rapport $rapport)
    {
    	$this->rapport->removeElement($rapport);
    }
    
    /**
     * Get rapport
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRapport()
    {
    	return $this->rapport;
    }
}
