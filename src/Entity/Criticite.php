<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CriticiteRepository;
/**
 * @ORM\Entity(repositoryClass=CriticiteRepository::class)
 */
class Criticite
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
     * @ORM\Column(name="libelle", type="string", length=25, nullable=false)
     */
    private $libelle;

    /**
     * @var integer
     * @ORM\Column(name="valeur_minimum", type="integer", length=2, nullable=false)
     */
    private $vmin;

    /**
     * @var integer
     * @ORM\Column(name="valeur_maximum", type="integer", length=2, nullable=false)
     */
    private $vmax;

    /**
     * @var integer
     * @ORM\Column(name="niveau", type="integer", length=1, nullable=false)
     */
    private $niveau;

    /**
     * @var string
     * @ORM\Column(name="couleur", type="string", length=25, nullable=false)
     */
    private $couleur;
    
    
    /**
     * @return number
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
	 * @return Criticite
	 */
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}
	
	/**
	 * @return integer
	 */
	public function getVmin() {
		return $this->vmin;
	}
	
	/**
	 * @param integer $vmin
	 * @return Criticite
	 */
	public function setVmin($vmin) {
		$this->vmin = $vmin;
		return $this;
	}
	
	/**
	 * @return integer
	 */
	public function getVmax() {
		return $this->vmax;
	}
	
	/**
	 * @param integer $vmax
	 * @return Criticite
	 */
	public function setVmax($vmax) {
		$this->vmax = $vmax;
		return $this;
	}
	
	/**
	 * @return integer
	 */
	public function getNiveau() {
		return $this->niveau;
	}
	
	/**
	 * @param integer $niveau
	 * @return Criticite
	 */
	public function setNiveau($niveau) {
		$this->niveau = $niveau;
		return $this;
	}
	
	/**
	 * get couleur
	 * @return string
	 */
	public function getCouleur() {
		return $this->couleur;
	}
	
	/**
	 * set couleur
	 * @param string $couleur
	 * @return Criticite
	 */
	public function setCouleur($couleur) {
		$this->couleur = $couleur;
		return $this;
	}
	
	/**
	 * get libelle
	 * @return string
	 */
	public function __toString() {
		return $this->libelle;
	}
}
