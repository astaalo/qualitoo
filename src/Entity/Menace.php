<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Menace
 *
 * @ORM\Table(name="menace")
 * @ORM\Entity(repositoryClass="App\Repository\MenaceRepository")
 */
class Menace
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
     * @ORM\Column(name="libelle", type="string", length=255, nullable=false)
     * @Assert\NotNull(message="Le nom du risque est obligatoire")
     */
    private $libelle;
    
    /**
     * @var string
     *
     * @ORM\Column(name="libelle_sans_carspecial", type="string", length=255, nullable=true)
     */
    private $libelleSansCarSpecial;
	
    /**
     * @var String
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Risque", mappedBy="menace")
     */
    protected $risque;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="MenaceAvere", mappedBy="menace")
     */
    private  $menaceAvere;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Cartographie")
     * @ORM\JoinTable(name="menace_has_profilrisque",
     * 	joinColumns={
     * 		@ORM\JoinColumn(name="menace_id", referencedColumnName="id")
     * 	}, 
     * 	inverseJoinColumns={
     * 		@ORM\JoinColumn(name="profil_risque_id", referencedColumnName="id")
     * })
     */
    private $cartographie;
	
    /**
     * @var boolean
     *
     * @ORM\Column(name="etat", type="boolean", nullable=true)
     */
    private $etat = true;
    
    public function __construct() {
    	$this->risque = new \Doctrine\Common\Collections\ArrayCollection();
    }
    

    /**
     * get id
     * @return integer
     */
	public function getId() {
		return $this->id;
	}

	/**
	 * get libelle
	 * @return string
	 */
	public function getLibelle() {
		return $this->libelle;
	}
	
	/**
	 * set libelle
	 * @param string $libelle
	 * @return Activite
	 */
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}

	/**
	 * get profil risque
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getCartographie() {
		return $this->cartographie;
	}
	
	/**
	 * set profil risque
	 * @param \Doctrine\Common\Collections\ArrayCollection
	 */
	public function setCartographie($cartographie) {
		$this->cartographie = $cartographie;
		return $this;
	}
	
	/**
	 * get profil risque's libelle
	 * @return string
	 */
	public function getLibelleCartographie() {
		$libelle = null;
		foreach($this->cartographie as $cartographie) {
			$libelle .= $cartographie->getLibelle();
			if($this->cartographie->last()!=$cartographie) {
				$libelle .= "<br>";
			}
		}
		return $libelle;
	}
	
	/**
	 * get description
	 * @return String
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * set description
	 * @param string $description
	 * @return Activite
	 */
	public function setDescription($description) {
		$this->description = $description;
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
	 * @return Activite
	 */
	public function setEtat($etat) {
		$this->etat = $etat;
		return $this;
	}
	
	public function getRisque() {
		return $this->risque;
	}
	
	/**
	 * Get libelle
	 * @return string
	 */
	public function __toString(){
		return $this->libelle;
	}
	
	

    /**
     * Add risque
     *
     * @param Risque $risque
     * @return Menace
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
     * Add menaceAvere
     *
     * @param MenaceAvere $menaceAvere
     * @return Menace
     */
    public function addMenaceAvere(MenaceAvere $menaceAvere)
    {
        $this->menaceAvere[] = $menaceAvere;
    
        return $this;
    }

    /**
     * Remove menaceAvere
     *
     * @param MenaceAvere $menaceAvere
     */
    public function removeMenaceAvere(MenaceAvere $menaceAvere)
    {
        $this->menaceAvere->removeElement($menaceAvere);
    }

    /**
     * Get menaceAvere
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMenaceAvere()
    {
        return $this->menaceAvere;
    }

    /**
     * Add cartographie
     *
     * @param Cartographie $cartographie
     * @return Menace
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
     * Set libelleSansCarSpecial
     *
     * @param string $libelleSansCarSpecial
     * @return Menace
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
