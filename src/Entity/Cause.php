<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Cause
 *
 * @ORM\Table(name="cause")
 * @ORM\Entity(repositoryClass="App\Repository\CauseRepository")
 */
class Cause
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
     * @ORM\Column(name="libelle", type="string", length=255, nullable=true)
     * @Assert\NotNull(message="Le libellé est obligatoire")
     */
    private $libelle;
    
    /**
     * @var string
     *
     * @ORM\Column(name="libelle_sans_carspecial", type="string", length=255, nullable=true)
     */
    private $libelleSansCarSpecial;
    
	
    /**
     * @var boolean
     *
     * @ORM\Column(name="etat", type="boolean", nullable=true)
     */
    private $etat = false;
    
    /**
     * @var String
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;
    
    /**
     * @var \Famille
     *
     * @ORM\ManyToOne(targetEntity="Famille")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="famille_id", referencedColumnName="id")
     * })
     */
    private $famille;
    
   /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="RisqueHasCause", mappedBy="cause", cascade={"persist", "merge"})
     */
    private $risqueHasCause;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="EvaluationHasCause", mappedBy="cause")
     */
    private $evaluation;
    
    /**
     * @var Processus
     */
    public $processus;
    
    /**
     * @var Structure
     */
    public $structure;
    
    /*
     * @var Cartographie
     
    public $cartographie;*/
    
    /**
     * @var Menace
     */
    public $menace;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Cartographie", orphanRemoval=true)
     * @ORM\JoinTable(name="cartographie_has_causes",
     * 	joinColumns={
     * 		@ORM\JoinColumn(name="cause_id", referencedColumnName="id")
     * 	},
     * 	inverseJoinColumns={
     * 		@ORM\JoinColumn(name="carto_id", referencedColumnName="id")
     * })
     */
    private $cartographie;
    
    public function __construct() {
    	$this->evaluation = new ArrayCollection();
    	$this->risqueHasCause=new ArrayCollection();
    	
    }
    
    /**
     * Get id
     * @return integer
     */
    public function getId() {
    	return $this->id;
    }
    
    /**
     * Get libelle
     * @return string
     */
	public function getLibelle() {
		return $this->libelle;
	}
	
	/**
	 * Set libelle
	 * @param string $libelle
	 * @return Cause
	 */
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}
	
	/**
	 * Get Famille
	 * @return Famille
	 */
	public function getFamille() {
		return $this->famille;
	}
	
	/**
	 * Set famille
	 * @param Famille $famille
	 * @return Cause
	 */
	public function setFamille($famille) {
		$this->famille = $famille;
		return $this;
	}
	
	/**
	 * get etat
	 * @return boolean
	 */
	public function getEtat() {
		return $this->etat;
	}
	
	/**
	 * set etat
	 * @param boolean $etat
	 * @return Cause
	 */
	public function setEtat($etat) {
		$this->etat = $etat;
		return $this;
	}
	
	/**
	 * set description
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * set description
	 * @param string $description
	 * @return Cause
	 */
	public function setDescription($description) {
		$this->description = $description;
		return $this;
	}
	
	/**
	 * @return array
	 */
	public function getArchFamille() {
		$data = array();
		$lastId = null;
		$famille = $this->famille;
		while($famille) {
			$data[$famille->getId()] = array('name'=>$famille->getLibelle(), 'level'=>$famille->getLvl(), 'children'=>$data, 'cause'=>array());
			if($lastId) {
				unset($data[$lastId]);
			}
			$lastId = $famille->getId();
			$famille = $famille->getParent();
		}
		return $data;
	}

	/**
	 * Get libelle
	 * @return string
	 */
	public function __toString() {
		return $this->libelle.'';
	}
	


    /**
     * Add evaluation
     *
     * @param EvaluationHasCause $evaluation
     * @return Cause
     */
    public function addEvaluation(EvaluationHasCause $evaluation)
    {
        $this->evaluation[] = $evaluation;
    
        return $this;
    }

    /**
     * Remove evaluation
     *
     * @param EvaluationHasCause $evaluation
     */
    public function removeEvaluation(EvaluationHasCause $evaluation)
    {
        $this->evaluation->removeElement($evaluation);
    }
    
    /**
     * has evaluation
     * @return bool
     */
    public function hasEvaluation() {
    	return $this->evaluation->count() > 0;
    }
    
	/**
	 *
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getEvaluation() {
		return $this->evaluation;
	}

    /**
     * Add risqueHasCause
     *
     * @param RisqueHasCause $risqueHasCause
     * @return Cause
     */
    public function addRisqueHasCause(RisqueHasCause $risqueHasCause)
    {
        $this->risqueHasCause[] = $risqueHasCause;
    
        return $this;
    }

    /**
     * Remove risqueHasCause
     *
     * @param RisqueHasCause $risqueHasCause
     */
    public function removeRisqueHasCause(RisqueHasCause $risqueHasCause)
    {
        $this->risqueHasCause->removeElement($risqueHasCause);
    }

    /**
     * Get risqueHasCause
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRisqueHasCause()
    {
        return $this->risqueHasCause;
    }

    /**
     * Add cartographie
     *
     * @param Cartographie $cartographie
     * @return Cause
     */
    public function addCartographie(Cartographie $cartographie)
    {
        $this->cartographie[] = $cartographie;
    
        return $this;
    }

    /**
     * Remove cartographie
     *
     * @param Cartographie $cartographie
     */
    public function removeCartographie(Cartographie $cartographie)
    {
        $this->cartographie->removeElement($cartographie);
    }

    /**
     * Get cartographie
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCartographie()
    {
        return $this->cartographie;
    }

    /**
     * Set libelleSansCarSpecial
     *
     * @param string $libelleSansCarSpecial
     * @return Cause
     */
    public function setLibelleSansCarSpecial($libelleSansCarSpecial)
    {
        $this->libelleSansCarSpecial = $libelleSansCarSpecial;
    
        return $this;
    }

    /**
     * Get libelleSansCarSpecial
     *
     * @return string 
     */
    public function getLibelleSansCarSpecial()
    {
        return $this->libelleSansCarSpecial;
    }
}
