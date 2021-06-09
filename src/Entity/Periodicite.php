<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Periodicite
 *
 * @ORM\Table(name="periodicite")
 * @ORM\Entity
 */
class Periodicite
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
     * @ORM\Column(name="libelle", type="string", length=45, nullable=false)
     */
    private $libelle;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="marge", type="integer", nullable=false)
     */
    private $marge;
    
    /**
     * @var Periodicite
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Periodicite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="marge_periodicite_id", referencedColumnName="id")
     * })
     */
    private $margePeriodicite;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="etat", type="boolean", nullable=false)
     */
    private $etat=true;
    
    
    /**
     * get Id
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
	 * @return Periodicite
	 */
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}
	
	/**
	 * get marge
	 * @return integer
	 */
	public function getMarge() {
		return $this->marge;
	}
	
	/**
	 * set marge
	 * @param integer $marge
	 * @return Periodicite
	 */
	public function setMarge($marge) {
		$this->marge = $marge;
		return $this;
	}
	
	/**
	 * get marge's periodicite
	 * @return Periodicite
	 */
	public function getMargePeriodicite() {
		return $this->margePeriodicite;
	}
	
	/**
	 * set marge's periodicite
	 * @param Periodicite $margePeriodicite
	 * @return Periodicite
	 */
	public function setMargePeriodicite($margePeriodicite) {
		$this->margePeriodicite = $margePeriodicite;
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
	 * @return Periodicite
	 */
	public function setEtat($etat) {
		$this->etat = $etat;
		return $this;
	}

	/**
	 * Get libelle
	 * @return string
	 */
	public function __toString(){
		return $this->libelle;
	}

}
