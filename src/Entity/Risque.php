<?php
namespace App\Entity;

use App\Controller\BaseController;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Absctract\NotificationInterface;
use App\Utils\NotificationUtil;
use App\Entity\RisqueProjet;

/**
 * Risque
 * @ORM\Table(name="risque")
 * @ORM\Entity(repositoryClass="App\Repository\RisqueRepository")
 */
class Risque implements NotificationInterface {
	static $profilRisqueIds;
	static $types;
	static $carto = [
        'metier' => 1,
        'projet' => 2,
        'sst' => 3,
        'environnement' => 4
    ];
	static $states = [
	    'abandonne' => -2,
        'rejete' =>    -1,
        'nouveau' => 0,
        'a_valider' =>  2,
        'en_cours' =>   3,
        'valide' =>     1,
        'transfere' =>  4
    ];


	
	/**
	 * @var integer 
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;
	
	/**
	 * @var integer @ORM\Column(name="numero", type="integer", length=11, nullable=true)
	 */
	private $numero;
	
	/**
	 * @var string @ORM\Column(name="code", type="string", length=45, nullable=true)
	 */
	private $code;
	
	/**
	 * @var Menace 
	 * @ORM\ManyToOne(targetEntity="Menace")
	 * @ORM\JoinColumns({
	 * 	@ORM\JoinColumn(name="menace_id", referencedColumnName="id")
	 * })
	 * @Assert\NotNull(message="Veuillez choisir le nom de risque s'il vous plait", groups={"RisqueValidation","RisqueIdentification"})
	 */
	private $menace;
	
	/**
	 * @var integer @ORM\Column(name="survenance", type="integer", nullable=true)
	 */
	private $survenance;
	
	/**
	 *
	 * @var integer 
	 * @ORM\Column(name="etat", columnDefinition="TINYINT(1) default 0")
	 */
	private $etat = 0;
	
	/**
	 * @var Identification 
	 * @ORM\OneToOne(targetEntity="Identification", mappedBy="risque", cascade={"persist", "merge","remove"})
	 * @Assert\Valid
	 */
	private $identification;
	
	/**
	 * @var Utilisateur 
	 * @ORM\ManyToOne(targetEntity="Utilisateur")
	 * @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
	 * })
	 */
	private $utilisateur;
	
	/**
	 * @var Societe 
	 * @ORM\ManyToOne(targetEntity="Societe")
	 * @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="societe_id", referencedColumnName="id")
	 * })
	 */
	private $societe;
	
	/**
	 * @var Utilisateur 
	 * @ORM\ManyToOne(targetEntity="Utilisateur")
	 * @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="validateur", referencedColumnName="id")
	 * })
	 */
	private $validateur;
	
    /**
     * @var boolean
     * @ORM\Column(name="first", type="boolean", nullable=true)
     */
    private $first = true;
	
	/**
	 * @var Processus
	 */
	private $processus;
	
	/**
	 * @var TypeProcessus
	 */
	public $typeProcessus;
	
	/**
	 * @var integer 
	 * @ORM\Column(name="probabilite", type="integer", nullable=true)
	 */
	private $probabilite;
	
   /**
     * @var Maturite
     * @ORM\ManyToOne(targetEntity="Maturite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="maturite_reel_id", referencedColumnName="id")
     * })
     */
    private $maturiteReel;
    
    /**
     * @var Maturite
     * @ORM\ManyToOne(targetEntity="Maturite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="maturite_theorique_id", referencedColumnName="id")
     * })
     */
    private $maturiteTheorique;
	
	/**
	 * @var integer 
	 * @ORM\Column(name="gravite", type="integer", nullable=true)
	 */
	private $gravite;
	
	/**
	 * @var Criticite 
	 * @ORM\ManyToOne(targetEntity="Criticite")
	 * @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="criticite_id", referencedColumnName="id")
	 * })
	 */
	private $criticite;

	/**
	 * @var \DateTime
	 * @ORM\Column(name="date_saisie", type="date", nullable=true)
	 * 
	 */
	private $dateSaisie;
	
	/**
	 * @var \DateTime
	 * @ORM\Column(name="date_validation", type="date", nullable=true)
	 * @Assert\NotNull(message="La date de début est obligatoire", groups={"Validation"})
	 */
	private $dateValidation;
	
	/**
	 * @var Cartographie 
	 * @ORM\ManyToOne(targetEntity="Cartographie", inversedBy="risque", fetch="EAGER")
	 * @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="cartographie_id", referencedColumnName="id")
	 * })
	 */
	private $cartographie;
	
	/**
	 * @var RisqueMetier 
	 * @ORM\OneToOne(targetEntity="RisqueMetier", mappedBy="risque", cascade={"persist", "merge","remove"})
	 */
	private $risqueMetier;
	
	/**
	 * @var RisqueProjet 
	 * @ORM\OneToOne(targetEntity="RisqueProjet", mappedBy="risque", cascade={"persist", "merge","remove"})
	 */
	private $risqueProjet;

	/**
	 * @var RisqueSST 
	 * @ORM\OneToOne(targetEntity="RisqueSST", mappedBy="risque", cascade={"persist", "merge","remove"})
	 */
	private $risqueSST;
	
	/**
	 * @var RisqueEnvironnemental 
	 * @ORM\OneToOne(targetEntity="RisqueEnvironnemental", mappedBy="risque", cascade={"persist", "merge","remove"})
	 */
	private $risqueEnvironnemental;
	
	/**
	 * @var Risque
	 * @ORM\OneToOne(targetEntity="Risque")
	 * @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="origine_id", referencedColumnName="id")
	 * })
	 */
	private $origine;
	
	/**
	 * @var \DateTime
	 * @ORM\Column(name="date_transfert", type="date", nullable=true)
	 * 
	 */
	private $dateTransfert;
	
	/**
	 * 
	 * @var Utilisateur
	 * @ORM\ManyToOne(targetEntity="Utilisateur")
	 * @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="acteur_transfert_id", referencedColumnName="id")
	 * })
	 * 
	 */
	private $acteurTransfert;
	
	/**
	 *
	 * @var \Doctrine\Common\Collections\ArrayCollection 
	 * @ORM\OneToMany(targetEntity="RisqueHasImpact", mappedBy="risque", cascade={"persist", "merge","remove"})
	 * @Assert\Valid
	 * @Assert\NotNull(message="Veuillez ajouter les impacts de ce risque", groups={"RisqueValidation"})
	 */
	private $impactOfRisque;
	
	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection 
	 * @ORM\OneToMany(targetEntity="RisqueHasCause", mappedBy="risque", orphanRemoval=true, cascade={"persist", "merge", "remove"})
	 * @Assert\Valid
	 * @Assert\NotNull(groups={"RisqueValidation","RisqueIdentification"})
	 */
	private $causeOfRisque;
	
	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @ORM\OneToMany(targetEntity="AuditHasRisque", mappedBy="risque", cascade={"persist", "merge", "remove"})
	 */
	private $auditHasRisque;
	
	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection 
	 * @ORM\OneToMany(targetEntity="Evaluation", mappedBy="risque", cascade={"persist", "merge", "remove"})
	 */
	private $evaluation;
	
	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	private $typeGrille;
	
	/**
	 * @var boolean
	 * @ORM\Column(name="to_be_migrated", type="boolean", nullable=false)
	 */
	private $tobeMigrated = true;
	
	/**
	 * @var boolean
	 * @ORM\Column(name="relanced", type="boolean", nullable=false)
	 */
	private $relanced=true;
	
	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @ORM\OneToMany(targetEntity="Repetition", mappedBy="risque", cascade={"persist", "merge", "remove"})
	 */
	private $repetitionRelances;
	
	/**
	 * @var \Doctrine\Common\Collections\Collection
	 * @ORM\ManyToMany(targetEntity="Chargement", mappedBy="risque", cascade={"persist", "merge"})
	 */
	private $chargement;
	
	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @ORM\OneToMany(targetEntity="NotificationRisque", mappedBy="risque", cascade={"persist", "merge", "remove"})
	 */
	private $notification;
	
	/**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="HistoryEtatRisque", mappedBy="risque", cascade={"persist", "merge", "remove"})
	 */
	private $historyEtat;
	
	/**
	 * @var HistoryEtatRisque
	 * @ORM\ManyToOne(targetEntity="HistoryEtatRisque")
	 * @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="history_etat_id", referencedColumnName="id")
	 * })
	 */
	private $lastHistory;
	
	
	/**
	 * @var Cause
	 */
	public $cause;
	
	/**
	 * @var array
	 */
	public $dateEvaluation;
	
	/**
	 * @var string
	 */
	public $toTransferts;
	
	// attributs pour les filtres des kpis
	/**
	 * @var integer
	 */
	public $probaForKpi;
	
	/**
	 * @var Maturite
	 */
	public $maturiteForKpi;
	
	/**
	 * @var integer
	 */
	public $graviteForKpi;
	
	/**
	 * @var integer
	 */
	public $occurencesForKpi;
	
	/**
	 * @var Criticite
	 */
	public $criticiteForKpi;
	
	/**
	 * @var Maturite
	 */
	public $maturiteReels;
	
	/**
	 * @var Maturite
	 */
	public $maturiteTheoriques;
	
	/**
	 * @var integer
	 */
	public $anneeEvaluationDebut;
	
	/**
	 * @var integer
	 */
	public $anneeEvaluationFin;
	
	/**
	 * @var boolean
	 */
	public $hasPlanAction;
	
	/**
	 * @var Statut
	 */
	public $statutPlanAction;
	
	/**
	 * @var boolean
	 */
	public $hasControle;
	
	/**
	 * @var string
	 */
	public $motCle;
	
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->causeOfRisque = new \Doctrine\Common\Collections\ArrayCollection();
		$this->impactOfRisque = new \Doctrine\Common\Collections\ArrayCollection();
		$this->dateSaisie=new \DateTime("NOW");
	}
	
	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Set id
	 * @return Risque
	 */
	public function setId($id) {
		$this->id=null;
		return $this;
	}
	
	/**
	 * Get code
	 *
	 * @return string
	 */
	public function getCode() {
		return $this->code;
	}
	
	/**
	 *
	 * @param string $code        	
	 * @return Risque
	 */
	public function setCode($code) {
		$this->code = $code;
		return $this;
	}
	
	/**
	 *
	 * @return integer
	 */
	public function getNumero() {
		return $this->numero;
	}
	
	/**
	 *
	 * @param integer $numero        	
	 * @return Risque
	 */
	public function setNumero($numero) {
		$this->numero = $numero;
		return $this;
	}
	
	/**
	 * get menace
	 * 
	 * @return Menace
	 */
	public function getMenace() {
		return $this->menace;
	}
	
	/**
	 * set menace
	 * 
	 * @param Menace $menace        	
	 * @return Menace
	 */
	public function setMenace($menace) {
		$this->menace = $menace;
		return $this;
	}
	public function getSurvenance() {
		return $this->survenance;
	}
	
	/**
	 * @param unknown $survenance        	
	 * @return Risque
	 */
	public function setSurvenance($survenance) {
		$this->survenance = $survenance;
		return $this;
	}
	
	/**
	 * get etat
	 * @return integer
	 */
	public function getEtat() {
		return $this->etat;
	}
	
	/**
	 *
	 * @param integer $etat        	
	 * @return Risque
	 */
	public function setEtat($etat) {
		$this->etat = $etat;
		return $this;
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getMainGrille() {
		$data = new \Doctrine\Common\Collections\ArrayCollection();
		foreach($this->cartographie->getTypeGrilleCause() as $typeGrille) {
			foreach($typeGrille->getGrille() as $grille) {
				$data->add($grille);
			}
		}
		return $data;
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getNormalGrille() {
		$data = new \Doctrine\Common\Collections\ArrayCollection();
		foreach($this->cartographie->getTypeGrilleCause() as $typeGrille) {
			foreach($typeGrille->getGrille() as $grille) {
				$data->add($grille);
			}
		}
		return $data;
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getAnormalGrille() {
		$data = new \Doctrine\Common\Collections\ArrayCollection();
		foreach($this->cartographie->getTypeGrilleCause() as $typeGrille) {
			foreach($typeGrille->getGrille() as $grille) {
				$data->add($grille);
			}
		}
		return $data;
	}
	
	/**
	 *
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getTypeGrille() {
		return $this->cartographie->getTypeGrille()->filter(function($typeGrille) {
			return $typeGrille->getEtat();
		} );
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getTypeGrilleBy($typeEvaluationId) {
		return $this->cartographie->getTypeGrille()->filter(function($typeGrille) use($typeEvaluationId) {
			return $typeGrille->getEtat() && $typeGrille->getTypeEvaluation()->getId() == $typeEvaluationId;
		} );
	}
	
	/**
	 * @return TypeGrille
	 */
	public function getTypeGrilleCauseBy($modeFonctionnementId) {
		$data = $this->cartographie->getTypeGrille()->filter(function($typeGrille) use($modeFonctionnementId) {
			return $typeGrille->getEtat() && $typeGrille->getTypeEvaluation()->getId() == TypeEvaluation::$ids['cause'] &&
				(($typeGrille->getModeFonctionnement()==null && $modeFonctionnementId==null) || 
						($typeGrille->getModeFonctionnement() && $typeGrille->getModeFonctionnement()->getId()==$modeFonctionnementId)
					);
			});
		return $data->count() ? $data->first() : null;
	}
	
	/**
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->menace ? $this->menace->getLibelle() :($this->identification ? $this->identification->getLibelle() : '');
	}
	
	/**
	 * get utilisateur
	 * 
	 * @return Utilisateur
	 */
	public function getUtilisateur() {
		return $this->utilisateur;
	}
	
	/**
	 * set utilisateur
	 * 
	 * @param Utilisateur $utilisateur
	 * @return Risque
	 */
	public function setUtilisateur($utilisateur) {
		$this->utilisateur = $utilisateur;
		return $this;
	}
	
	/**
	 * get validateur
	 * 
	 * @return Utilisateur
	 */
	public function getValidateur() {
		return $this->validateur;
	}
	
	/**
	 * set validateur
	 * 
	 * @param Utilisateur $validateur
	 * @return Risque
	 */
	public function setValidateur($validateur) {
		$this->validateur = $validateur;
		return $this;
	}
	
	/**
	 * Get Probabilite
	 * 
	 * @return integer
	 */
	public function getProbabilite() {
		return $this->probabilite;
	}
	
	/**
	 * Set Probabilite
	 * 
	 * @param integer $probabilite        	
	 * @return Risque
	 */
	public function setProbabilite($probabilite) {
		$this->probabilite = $probabilite;
		return $this;
	}
	
	/**
	 * Get Gravite
	 * 
	 * @return integer
	 */
	public function getGravite() {
		return $this->gravite;
	}
	
	/**
	 * Set Gravite
	 * 
	 * @param integer $gravite        	
	 * @return Risque
	 */
	public function setGravite($gravite) {
		$this->gravite = $gravite;
		return $this;
	}
	
	/**
	 * Get Criticite
	 * 
	 * @return Criticite
	 */
	public function getCriticite() {
		return $this->criticite;
	}
	
	/**
	 * Set Criticite
	 * 
	 * @param Criticite $criticite        	
	 * @return Risque
	 */
	public function setCriticite($criticite) {
		$this->criticite = $criticite;
		return $this;
	}
	
	/**
	 * Get Plan d'action
	 *
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getPlanAction() {
		$data = new \Doctrine\Common\Collections\ArrayCollection();
		foreach($this->causeOfRisque as $cor) {
			foreach($cor->getPlanAction() as $planAction) {
				$data->add($planAction);
			}
		}
		return $data;
	}
	
	/**
	 * Get Controle
	 * 
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getControle() {
		$data = new \Doctrine\Common\Collections\ArrayCollection();
		foreach($this->causeOfRisque as $cor) {
			foreach($cor->getControle() as $controle) {
				$data->add($controle );
			}
		}
		return $data;
	}
	
	/**
	 *
	 * @return array
	 */
	public function getAllProcessus() {
		$data = array();
		$processus = $this->getActivite()->getProcessus();
		while($processus) {
			$data[$processus->getId()] = array(
					'type' => $processus->getTypeProcessus()->getId(),
					'name' => $processus->getLibelle() 
			);
			$processus = $processus->getParent();
		}
		return $data;
	}
	
	/**
	 *
	 * @return array
	 */
	public function getArchProcessus() {
		$data = array();
		$lastId = null;
		$processus = $this->getActivite()?$this->getActivite()->getProcessus():null;
		while($processus) {
			$data[$processus->getId()] = array(
					'type' => $processus->getTypeProcessus()->getId(),
					'name' => $processus->getLibelle(),
					'children' => $data,
					'activite' => array(),
					'structure' => sprintf('%s(%s)', $processus->getStructure()->getLibelle(), $processus->getStructure()->getCode())
			);
			if($lastId) {
				unset($data[$lastId] );
			}
			$lastId = $processus->getId();
			$processus = $processus->getParent();
		}
		return $data;
	}
	
	/**
	 *
	 * @return Processus
	 */
	public function getProcessusByType($type) {
		$processus = $this->getActivite()->getProcessus();
		while($processus) {
			if($processus->getTypeProcessus()->getId() == $type) {
				break;
			}
			$processus = $processus->getParent();
		}
		return $processus;
	}
	
	/**
	 * Get Controle's number
	 *
	 * @return integer
	 */
	public function getNumberControle() {
		$number = 0;
		foreach($this->planAction as $planAction) {
			if($planAction->getControle()) {
				$number += 1;
			}
		}
		foreach($this->controle as $controle) {
			if($controle->getPlanAction() == null) {
				$number += 1;
			}
		}
		return $number;
	}
	
	/**
	 * Get Identification
	 *
	 * @return Identification
	 */
	public function getIdentification() {
		return $this->identification;
	}
	
	/**
	 * Get ChampComplementaire
	 *
	 * @return ChampComplementaire
	 */
	public function getComplementary() {
		return $this->complementary;
	}
	
	/**
	 * Set Identification
	 *
	 * @param Identification $identification        	
	 * @return Risque
	 */
	public function setIdentification(Identification $identification) {
		$identification->setRisque($this);
		$this->identification = $identification;
		return $this;
	}
	
	/**
	 *
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getCauseOfRisque() {
		return $this->causeOfRisque;
	}
	
	/**
	 *
	 * @param RisqueHasCause $causeOfRisque
	 * @return Risque
	 */
	public function addCauseOfRisque($causeOfRisque) {
		$causeOfRisque->setRisque($this);
		$this->causeOfRisque[] = $causeOfRisque;
		return $this;
	}
	
	/**
	 * remove risque's cause
	 * 
	 * @param RisqueHasCause $causeOfRisque
	 * @return Risque
	 */
	public function removeCauseOfRisque($causeOfRisque) {
		$this->causeOfRisque->removeElement($causeOfRisque );
		return $this;
	}
	
	public function clearCauseOfRisque() {
		$this->causeOfRisque = new \Doctrine\Common\Collections\ArrayCollection();
	}
	
	public function clearImpactOfRisque() {
		foreach($this->impactOfRisque as $impactOfRisque) {
			$this->impactOfRisque->removeElement($impactOfRisque);
		}
		return $this;
	}
	
	/**
	 *
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getImpactOfRisque() {
		return $this->impactOfRisque;
	}
	
	
	/**
	 * @param Domaine $domaine
	 * @return RisqueHasImpact
	 */
	public function getImpactOfRisqueByDomaine($domaine) {
		$data = $this->impactOfRisque->filter(function($impactOfRisque) use($domaine) {
			$d = $impactOfRisque->getImpact()->getCritere();
			//var_dump($d); exit;
			return $d != null ? $d->getDomaine()->getId() == $domaine->getId() : $d;
		});
		return $data->count() ? $data->first() : null;
	}
	
	/**
	 *
	 * @param RisqueHasImpact $impactOfRisque
	 * @return Risque
	 */
	public function addImpactOfRisque($impactOfRisque) {
		$impactOfRisque->setRisque($this);
		$this->impactOfRisque[] = $impactOfRisque;
		return $this;
	}
	
	/**
	 * remove risque's impact
	 * 
	 * @param RisqueHasImpact $impactOfRisque
	 * @return Risque
	 */
	public function removeImpactOfRisque($impactOfRisque) {
		$this->impactOfRisque->removeElement($impactOfRisque);
		return $this;
	}
	
	public function setAllImpact($data) {
		$this->clearImpactOfRisque();
		$this->impactOfRisque = $data;
		return $this;
	}
	
	/**
	 * Get Evaluation
	 *
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getEvaluation() {
		return $this->evaluation;
	}
	
	/**
	 * Add evaluation
	 *
	 * @param Evaluation $evaluation        	
	 * @return Risque
	 */
	public function addEvaluation($evaluation) {
		$evaluation->setRisque($this );
		$this->evaluation->add($evaluation );
		return $this;
	}
	
	/**
	 * @return Mixed
	 */
	public function getRisqueData() {
        $carto = self::$carto;
		if($this->cartographie==null) {
			return null;
		}
		$data = null;
		switch($this->cartographie->getId()) {
			case $carto['metier']:
				$data = $this->risqueMetier;
				break;
			case $carto['projet']:
				$data = $this->risqueProjet;
				break;
			case $carto['sst']:
				$data = $this->risqueSST;
				break;
			case $carto['environnement']:
				$data = $this->risqueEnvironnemental;
				break;
		}
		return $data;
	}
	
	public function isValidated() {
		return $this->getEtat() == Risque::$states['valide'];
	}
	public function hasToBeValidated() {
		return $this->getEtat() == Risque::$states['a_valider'];
	}
	public function setHasToBeValidated($hasToBeValidated) {
		$this->etat = Risque::$states['a_valider'];
		return $this;
	}
	public function isRejected() {
		return $this->getEtat() == Risque::$states['rejete'];
	}
	public function isPending() {
		return $this->getEtat() == Risque::$states['en_cours'];
	}
	public function isIdentified() {
		return $this->getEtat() == Risque::$states['nouveau'];
	}
	
	/**
	 * Remove evaluation
	 *
	 * @param Evaluation $evaluation
	 */
	public function removeEvaluation(Evaluation $evaluation) {
		$this->evaluation->removeElement($evaluation );
	}
	
	/**
	 * Add planAction
	 *
	 * @param PlanAction $planAction
	 * @return Risque
	 */
	public function addPlanAction(PlanAction $planAction) {
		$this->planAction[] = $planAction;
		
		return $this;
	}
	
	/**
	 * Remove planAction
	 *
	 * @param PlanAction $planAction
	 */
	public function removePlanAction(PlanAction $planAction) {
		$this->planAction->removeElement($planAction );
	}
	
	/**
	 * Add controle
	 * @param Controle $controle
	 * @return Risque
	 */
	public function addControle(Controle $controle) {
		$this->controle[] = $controle;
		
		return $this;
	}
	
	/**
	 * Remove controle
	 * @param Controle $controle
	 */
	public function removeControle(Controle $controle) {
		$this->controle->removeElement($controle );
	}
	
	/*
	 *(non-PHPdoc)
	 * @see Absctract\NotificationInterface::generateNotification()
	 */
	public function generateNotification(Utilisateur $user, TypeNotification $type, $isNew) {
		// TODO: Auto-generated method stub
		if($isNew) {
			$label = 'Identification d\'un nouveau risque';
			$desc = sprintf('Bonjour, <br> <b> %s </b> a identifié un nouveau risque : <span style="color: #f60;">%s</span>', $user, $this );
		} else {
			$label = 'Validation d\'un risque';
			$desc = sprintf('Bonjour, <br> <b> %s </b> vient de valider le risque : <span style="color: #f60;">%s</span>', $user, $this );
		}
		if($this->getCartographie()->getId()  <= 2) {
			$manager  = $this->getRisqueData()->getStructure()
							  ? ($this->getRisqueData()->getStructure()->getManager()?$this->getRisqueData()->getStructure()->getManager() : null)
							  : null;
		} else {
			$manager  = $this->getRisqueData()->getSite()
								? ($this->getRisqueData()->getSite()->getResponsable()?$this->getRisqueData()->getSite()->getResponsable() : null)
								: null;
		}
		$notification = NotificationUtil::create($label, $desc, $this, $type, $user );
		if($manager) {
			$notification->addReceiver($manager);
		}
		$riskManagers = $this->getSociete() && $this->getSociete()->getRiskManager() ? $this->getSociete()->getRiskManager() : null;
		if($riskManagers) {
			foreach($riskManagers as $riskManager) {
				$notification->addReceiver($riskManager );
			}
		}
		return $notification;
	}
	
	/*
	 *(non-PHPdoc)
	 * @see Absctract\NotificationInterface::generateWorkflow()
	 */
	public function generateWorkflow(Notification $notification, TypeNotification $type, $isNew) {
		// TODO: Auto-generated method stub
		$mail = array();
		
		if(! $this->isValidated()) {
			$subject = 'Identification d\'un nouveau risque'. $this->getCartographie()->getLibelle();
			$action = 'Identification de risque';
			$content = sprintf('Bonjour, <br> <b> %s </b> a identifié un nouveau risque : <span style="color: #f60;">%s</span>', $notification->getUser(), $this );
		} else {
			$subject = 'Validation d\'un risque';
			$action = 'Validation de risque';
			$content = sprintf('Bonjour, <br> <b> %s </b> a validé le risque : <span style="color: #f60;">%s</span>', $notification->getUser(), $this );
		}
		
// 		if($this->getActivite()) {
// 			$content .= ', pour l\'activité <b>"' . $this->getActivite() . '"</b>';
// 		}
		
		$mail['subject'] = $subject;
		$mail['body']['action'] = $action;
		$mail['body']['content'] = $content;
		$mail['body']['link'] = 'https://sigr.orange-sonatel.com';
		$mail['recipients'] = array();
		
		$mail['recipients'][] = $notification->getUser()->getEmail();
		
		foreach($notification->getReceivers() as $receiver) {
			$mail['recipients'][] = $receiver->getEmail();
		}
		$manager=null;
		if($this->getCartographie()->getId()  <= 2)
				$manager  = $this->getRisqueData()->getStructure()
				?($this->getRisqueData()->getStructure()->getManager()?$this->getRisqueData()->getStructure()->getManager():null)
				:null;
		else
				$manager  = $this->getRisqueData()->getSite()
				?($this->getRisqueData()->getSite()->getResponsable()?$this->getRisqueData()->getSite()->getResponsable():null)
				:null;
		if($manager)
			$mail['recipients'][]=$manager->getEmail();
		
		$mail['recipients']=array_unique($mail['recipients']);
		return $mail;
	}
	
	/**
	 * Set cartographie
	 *
	 * @param Cartographie $cartographie
	 * @return Risque
	 */
	public function setCartographie(Cartographie $cartographie = null) {
		$this->cartographie = $cartographie;
		
		return $this;
	}
	
	/**
	 * Get cartographie
	 *
	 * @return Cartographie
	 */
	public function getCartographie() {
		return $this->cartographie;
	}
	
	/**
	 * check if risque is SST or environmental
	 */
	public function isPhysical() {
		return in_array($this->cartographie->getId(), array(Cartographie::$ids['sst'], Cartographie::$ids['environnement']));
	}
	
	/**
	 * Set societe
	 *
	 * @param Societe $societe
	 * @return Risque
	 */
	public function setSociete(Societe $societe = null) {
		$this->societe = $societe;
		
		return $this;
	}
	
	/**
	 * Get societe
	 * @return Societe
	 */
	public function getSociete() {
		return $this->societe;
	}
	
	/**
	 * Get societe
	 * @return Structure
	 */
	public function getDirection() {
		$direction = null;
		if($this->risqueMetier) {
			$direction = $this->risqueMetier->getDirection();
		} elseif($this->risqueProjet) {
			$direction = $this->risqueProjet->getDirection();
		} else {
			$direction = null;
		}
		return $direction;
	}
	
	/**
	 * Get site
	 * @return Site
	 */
	public function getSite() {
		$site = null;
		if($this->risqueSST) {
			$site = $this->risqueSST->getSite();
		} elseif($this->risqueEnvironnemental) {
			$site = $this->risqueEnvironnemental->getSite();
		} else {
			$site = null;
		}
		return $site;
	}
	
	/**
	 * Set risqueMetier
	 *
	 * @param RisqueMetier $risqueMetier
	 * @return Risque
	 */
	public function setRisqueMetier(RisqueMetier $risqueMetier = null) {
		$this->risqueMetier = $risqueMetier;
		
		return $this;
	}
	
	/**
	 * Get risqueMetier
	 *
	 * @return RisqueMetier
	 */
	public function getRisqueMetier() {
		return $this->risqueMetier;
	}
	
	/**
	 * check if is risqueMetier
	 * @return boolean
	 */
	public function isRisqueMetier() {
		return $this->cartographie->getId()==Cartographie::$ids['metier'];
	}

    /**
     * Set risqueProjet
     *
     * @param RisqueProjet $risqueProjet
     *
     * @return Risque
     */
    public function setRisqueProjet(RisqueProjet $risqueProjet = null)
    {
        $this->risqueProjet = $risqueProjet;
    
        return $this;
    }

    /**
     * Get risqueProjet
     *
     * @return RisqueProjet
     */
    public function getRisqueProjet()
    {
        return $this->risqueProjet;
    }
	
	/**
	 * check if is risqueProjet
	 * @return boolean
	 */
	public function isRisqueProjet() {
		return $this->cartographie->getId()==Cartographie::$ids['projet'];
	}

    /**
     * Set risqueSST
     *
     * @param RisqueSST $risqueSST
     *
     * @return Risque
     */
    public function setRisqueSST(RisqueSST $risqueSST = null)
    {
        $this->risqueSST = $risqueSST;
    
        return $this;
    }

    /**
     * Get risqueSST
     *
     * @return RisqueSST
     */
    public function getRisqueSST()
    {
        return $this->risqueSST;
    }
	
	/**
	 * check if is risqueSST
	 * @return boolean
	 */
	public function isRisqueSST() {
		return $this->cartographie->getId()==Cartographie::$ids['sst'];
	}

    /**
     * Set risqueEnvironnemental
     *
     * @param RisqueEnvironnemental $risqueEnvironnemental
     *
     * @return Risque
     */
    public function setRisqueEnvironnemental(RisqueEnvironnemental $risqueEnvironnemental = null)
    {
        $this->risqueEnvironnemental = $risqueEnvironnemental;
    
        return $this;
    }

    /**
     * Get risqueEnvironnemental
     *
     * @return RisqueEnvironnemental
     */
    public function getRisqueEnvironnemental()
    {
        return $this->risqueEnvironnemental;
    }
	
	/**
	 * check if is risqueEnvironnemental
	 * @return boolean
	 */
	public function isRisqueEnvironnemental() {
		return $this->cartographie->getId()==Cartographie::$ids['environnement'];
	}
    
    public function getActivite() {
    	if($this->risqueMetier){
    		return $this->risqueMetier->getActivite();
    	}elseif($this->risqueSST){
    		return $this->risqueSST->getEquipement();
    	}elseif ($this->risqueProjet){
    		return $this->risqueProjet->getProjet();
    	}elseif ($this->risqueEnvironnemental){
    		return $this->risqueEnvironnemental->getEquipement();
    	}else{
    		
    	}
    }
    
    public function getResponsable() {
    	if($this->risqueMetier){
    		return $this->risqueMetier->getResponsable();
    	}
    }
    
    public function getStructreOrSite() {
        $carto = self::$carto;
    	switch($this->cartographie->getId()) {
    		case $carto['metier']:
    			$libelle = $this->risqueMetier ? $this->risqueMetier->getStructure() : null;
    		break;
    		case $carto['projet']:
    			$libelle = $this->risqueProjet ? $this->risqueProjet->getStructure() : null;
    		break;
    		case $carto['sst']:
    			$libelle = $this->risqueSST  ? $this->risqueSST->getSite() : null;
    		break;
    		case $carto['environnement']:
    			$libelle = $this->risqueEnvironnemental ? $this->risqueEnvironnemental->getSite() : null;
    		break;
    		default:
    			$libelle = null;
    	}
    	return $libelle;
    }
    
    public function toArray() {
    	$data = array();
    	foreach($this as $key => $value) {
    		if($value && is_object($value)) {
    			$data[$key] = $value->getId();
    		} elseif($value) {
    			$data[$key] = $value;
    		}
    	}
    	return $data;
    }

    /**
     * Set dateSaisie
     *
     * @param \DateTime $dateSaisie
     *
     * @return Risque
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
     * Set dateValidation
     *
     * @param \DateTime $dateValidation
     *
     * @return Risque
     */
    public function setDateValidation($dateValidation)
    {
        $this->dateValidation = $dateValidation;
    
        return $this;
    }

    /**
     * Get dateValidation
     *
     * @return \DateTime
     */
    public function getDateValidation()
    {
        return $this->dateValidation;
    }

    /**
     * Set dateTransfert
     *
     * @param \DateTime $dateTransfert
     * @return Risque
     */
    public function setDateTransfert($dateTransfert)
    {
        $this->dateTransfert = $dateTransfert;
    
        return $this;
    }

    /**
     * Get dateTransfert
     *
     * @return \DateTime 
     */
    public function getDateTransfert()
    {
        return $this->dateTransfert;
    }

    /**
     * Set origine
     *
     * @param Risque $origine
     * @return Risque
     */
    public function setOrigine(Risque $origine = null)
    {
        $this->origine = $origine;
    
        return $this;
    }

    /**
     * Get origine
     *
     * @return Risque
     */
    public function getOrigine()
    {
        return $this->origine;
    }

    /**
     * Set acteurTransfert
     *
     * @param Utilisateur $acteurTransfert
     * @return Risque
     */
    public function setActeurTransfert(Utilisateur $acteurTransfert = null)
    {
        $this->acteurTransfert = $acteurTransfert;
    
        return $this;
    }

    /**
     * Get acteurTransfert
     *
     * @return Utilisateur
     */
    public function getActeurTransfert()
    {
        return $this->acteurTransfert;
    }
    

    /**
     * Set first
     *
     * @param boolean $first
     * @return Risque
     */
    public function setFirst($first)
    {
        $this->first = $first;
    
        return $this;
    }

    /**
     * Get first
     *
     * @return boolean 
     */
    public function getFirst()
    {
        return $this->first;
    }


    /**
     * Set relanced
     *
     * @param boolean $relanced
     * @return Risque
     */
    public function setRelanced($relanced)
    {
        $this->relanced = $relanced;
    
        return $this;
    }

    /**
     * Get relanced
     *
     * @return boolean 
     */
    public function getRelanced()
    {
        return $this->relanced;
    }

    /**
     * Add repetitionRelances
     *
     * @param Repetition $repetitionRelances
     * @return Risque
     */
    public function addRepetitionRelance(Repetition $repetitionRelances)
    {
        $this->repetitionRelances[] = $repetitionRelances;
    
        return $this;
    }

    /**
     * Remove repetitionRelances
     *
     * @param Repetition $repetitionRelances
     */
    public function removeRepetitionRelance(Repetition $repetitionRelances)
    {
        $this->repetitionRelances->removeElement($repetitionRelances);
    }

    /**
     * Get repetitionRelances
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRepetitionRelances()
    {
        return $this->repetitionRelances;
    }


    /**
     * Add auditHasRisque
     *
     * @param AuditHasRisque $auditHasRisque
     * @return Risque
     */
    public function addAuditHasRisque(AuditHasRisque $auditHasRisque)
    {
        $this->auditHasRisque[] = $auditHasRisque;
    
        return $this;
    }

    /**
     * Remove auditHasRisque
     *
     * @param AuditHasRisque $auditHasRisque
     */
    public function removeAuditHasRisque(AuditHasRisque $auditHasRisque)
    {
        $this->auditHasRisque->removeElement($auditHasRisque);
    }

    /**
     * Get auditHasRisque
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAuditHasRisque()
    {
        return $this->auditHasRisque;
    }

    /**
     * Set maturiteReel
     *
     * @param Maturite $maturiteReel
     * @return Risque
     */
    public function setMaturiteReel(Maturite $maturiteReel = null)
    {
        $this->maturiteReel = $maturiteReel;
    
        return $this;
    }

    /**
     * Get maturiteReel
     *
     * @return Maturite
     */
    public function getMaturiteReel()
    {
        return $this->maturiteReel;
    }

    /**
     * Set maturiteTheorique
     *
     * @param Maturite $maturiteTheorique
     * @return Risque
     */
    public function setMaturiteTheorique(Maturite $maturiteTheorique = null)
    {
        $this->maturiteTheorique = $maturiteTheorique;
    
        return $this;
    }

    /**
     * Get maturiteTheorique
     *
     * @return Maturite
     */
    public function getMaturiteTheorique()
    {
        return $this->maturiteTheorique;
    }

    /**
     * Add chargement
     *
     * @param Risque $chargement
     * @return Risque
     */
    public function addChargement(Risque $chargement)
    {
        $this->chargement[] = $chargement;
    
        return $this;
    }

    /**
     * Remove chargement
     *
     * @param Risque $chargement
     */
    public function removeChargement(Risque $chargement)
    {
        $this->chargement->removeElement($chargement);
    }

    /**
     * Get chargement
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChargement()
    {
        return $this->chargement;
    }

    /**
     * Add historyEtat
     *
     * @param HistoryEtatRisque $historyEtat
     * @return Risque
     */
    public function addHistoryEtat(HistoryEtatRisque $historyEtat)
    {
        $this->historyEtat[] = $historyEtat;
    
        return $this;
    }

    /**
     * Remove historyEtat
     *
     * @param HistoryEtatRisque $historyEtat
     */
    public function removeHistoryEtat(HistoryEtatRisque $historyEtat)
    {
        $this->historyEtat->removeElement($historyEtat);
    }

    /**
     * Get historyEtat
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHistoryEtat()
    {
        return $this->historyEtat;
    }
    

    /**
     * Set lastHistory
     *
     * @param HistoryEtatRisque $lastHistory
     * @return Risque
     */
    public function setLastHistory(HistoryEtatRisque $lastHistory = null)
    {
        $this->lastHistory = $lastHistory;
    
        return $this;
    }

    /**
     * Get lastHistory
     *
     * @return HistoryEtatRisque
     */
    public function getLastHistory()
    {
        return $this->lastHistory;
    }

    /**
     * Add notification
     *
     * @param NotificationRisque $notification
     *
     * @return Risque
     */
    public function addNotification(NotificationRisque $notification)
    {
        $this->notification[] = $notification;

        return $this;
    }

    /**
     * Remove notification
     *
     * @param NotificationRisque $notification
     */
    public function removeNotification(NotificationRisque $notification)
    {
        $this->notification->removeElement($notification);
    }

    /**
     * Get notification
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotification()
    {
        return $this->notification;
    }
    
    /**
     * check if it is to migrate
     * @return Risque
     */
    public function getTobeMigrate() {
    	return $this->tobeMigrated;
    }
    
    /**
     * @param boolean $tobeMigrate
     * @return Risque
     */
    public function setTobeMigrate($tobeMigrate) {
    	$this->tobeMigrated = $tobeMigrate;
    	return $this;
    }
    
    /**
     * @return string
     */
    public function showEvaluationAsToMigrate() {
    	$arrData = array();
    	$data = array('risque' => $this->id, 'cartographie' => $this->cartographie->getId());
    	$data['menace'] = array('id' => $this->menace->getId(), 'libelle'=>$this->menace->getLibelle());
    	if(in_array($this->cartographie->getId(), array(Cartographie::$ids['metier'], Cartographie::$ids['projet']))) {
    		$risqueData = $this->cartographie->getId()==Cartographie::$ids['metier'] ? $this->risqueMetier : $this->risqueProjet;
    		if($risqueData==null) {
    			return null;
    		}
    		if($risqueData->getProcessus()) {
    			$data['processus'] = $risqueData->getProcessus()->showIdsToArray();
    		}
    		if(null!=$structure=$risqueData->getStructure()) {
    			$data['structure'] = array('id' => $structure->getId());
    			$data['societe'] = $structure->getSociete()->getId();
    			if(null!=$direction=$risqueData->getDirection()) {
    				$data['direction'] = array('id' => $direction->getId());
    			}
    		}
    		if($this->cartographie->getId()==Cartographie::$ids['metier'] && $risqueData->getActivite()) {
    			$data['activite'] = array('id' => $risqueData->getActivite()->getId());
    		} elseif($this->cartographie->getId()==Cartographie::$ids['projet'] && $risqueData->getProjet()) {
    			$data['projet'] = array('id' => $risqueData->getProjet()->getId());
    		}
    	}
    	if(in_array($this->cartographie->getId(), array(Cartographie::$ids['sst'], Cartographie::$ids['environnement']))) {
    		$risqueData = $this->cartographie->getId()==Cartographie::$ids['sst'] ? $this->risqueSST : $this->risqueEnvironnemental;
    		if($risqueData==null) {
    			return null;
    		}
    		if($risqueData->getSite()) {
    			$data['site'] = array('id' => $risqueData->getSite()->getId());
    			$data['societe'] = $risqueData->getSite()->getSociete()->getId();
    		}
    		if($risqueData->getEquipement()) {
    			$data['equipement'] = array('id' => $risqueData->getEquipement()->getId());
    		}
    	}
    	foreach($this->evaluation as $evaluation) {
    		$arrData[$evaluation->getAnnee()] = array_merge($data, $evaluation->showValuesAsToMigrate());
    	}
    	$annee = $this->evaluation->count() ? $this->evaluation->first()->getAnnee() : null;
    	if($annee==null) {
    		return array();
    	}
    	for($index=$annee;$index<=(int)date('Y');$index++) {
    		if(!isset($arrData[$index])) {
    			$arrData[$index] = $arrData[$index - 1];
    			$arrData[$index]['annee'] = $index;
    		}
    	}
    	return $arrData;
    }
    
    /**
     * @return string
     */
    public function showValuesAsToMigrate() {
    	$data = array('risque' => $this->id, 'cartographie' => $this->cartographie->getId(), 'causes' => array(), 'impacts' => array());
    	$data['menace'] = array('id' => $this->menace->getId(), 'libelle'=>$this->menace->getLibelle());
    	if(in_array($this->cartographie->getId(), array(Cartographie::$ids['metier'], Cartographie::$ids['projet']))) {
    		$risqueData = $this->cartographie->getId()==Cartographie::$ids['metier'] ? $this->risqueMetier : $this->risqueProjet;
    		if($risqueData==null) {
    			return null;
    		}
    		if($risqueData->getProcessus()) {
    			$data['processus'] = $risqueData->getProcessus()->showValuesToArray();
    		}
    		if(null!=$structure=$risqueData->getStructure()) {
    			$data['structure'] = array('id' => $structure->getId(), 'libelle'=> $structure->getLibelle(), 'name' => $structure->getName());
    			$data['societe'] = $risqueData->getStructure()->getSociete()->getId();
    			if(null!=$direction=$risqueData->getDirection()) {
    				$data['direction'] = array('id'=>$direction->getId(), 'name'=>$direction->getName(), 'libelle'=>$direction->getLibelle());
    			}
    		}
    		if($this->cartographie->getId()==Cartographie::$ids['metier'] && $risqueData->getActivite()) {
    			$data['activite'] = array('id' => $risqueData->getActivite()->getId(), 'libelle'=>$risqueData->getActivite()->getLibelle());
    		} elseif($this->cartographie->getId()==Cartographie::$ids['projet'] && $risqueData->getProjet()) {
    			$data['projet'] = array('id' => $risqueData->getProjet()->getId(), 'libelle'=>$risqueData->getProjet()->getLibelle());
    		}
    	}
    	if(in_array($this->cartographie->getId(), array(Cartographie::$ids['sst'], Cartographie::$ids['environnement']))) {
    		$risqueData = $this->cartographie->getId()==Cartographie::$ids['sst'] ? $this->risqueSST : $this->risqueEnvironnemental;
    		if($risqueData==null) {
    			return null;
    		}
    		if($risqueData->getSite()) {
    			$data['site'] = array('id' => $risqueData->getSite()->getId(), 'libelle'=>$risqueData->getSite()->getLibelle());
    			$data['societe'] = $risqueData->getSite()->getSociete()->getId();
    		}
    		if($risqueData->getEquipement()) {
    			$data['equipement'] = array('id' => $risqueData->getEquipement()->getId(), 'libelle'=>$risqueData->getEquipement()->getLibelle());
    		}
    	}
    	foreach($this->causeOfRisque as $cor) {
    		$data['causes'][] = array(
    				'id'=>$cor->getCause()->getId(),'libelle'=>$cor->getCause()->getLibelle(), 'probabilite'=>$cor->getProbabilite()
    		);
    	}
    	foreach($this->impactOfRisque as $ior) {
    		if($ior->getImpact()==null || $ior->getImpact()->getCritere()==null || $ior->getGrille()==null) {
    			continue;
    		}
    		$domaine=$ior->getImpact()->getCritere()->getDomaine();
    		if($domaine && $ior->getGrille()) {
    			$data['impacts'][] = array('id'=>$domaine->getId(),'libelle'=>$domaine->getLibelle(), 'gravite'=>$ior->getGrille()->getNote()->getValeur());
    		}
    	}
    	$data['probabilite'] = $this->probabilite;
    	$data['gravite'] = $this->gravite;
    	$data['criticite'] = $this->criticite ? $this->criticite->getNiveau() : null;
    	$data['maturite'] = $this->maturiteTheorique ? $this->maturiteTheorique->getValeur() : null;
    	return $data;
    }
}
