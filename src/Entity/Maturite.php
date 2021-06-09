<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Maturite
 *
 * @ORM\Table(name="maturite")
 * @ORM\Entity
 */
class Maturite {
	/**
	 *
	 * @var integer 
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;
	
	/**
	 *
	 * @var string @ORM\Column(name="libelle", type="string", length=45, nullable=false)
	 */
	private $libelle;
	
	/**
	 *
	 * @var string @ORM\Column(name="valeur", type="integer", nullable=false)
	 */
	private $valeur;
	
	/**
	 *
	 * @var integer @ORM\Column(name="couleur", type="string", nullable=true)
	 */
	private $couleur;
	
	public function getId() {
		return $this->id;
	}
	
	public function getLibelle() {
		return $this->libelle;
	}
	
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}
	
	public function getValeur() {
		return $this->valeur;
	}
	
	public function setCouleur($couleur) {
		$this->couleur = $couleur;
		return $this;
	}
	
	public function getCouleur() {
		return $this->couleur;
	}
	
	public function setEtat($etat) {
		$this->etat = $etat;
		return $this;
	}
	
	public function __toString() {
		return $this->libelle;
	}

    /**
     * Set valeur
     *
     * @param integer $valeur
     * @return Maturite
     */
    public function setValeur($valeur)
    {
        $this->valeur = $valeur;
    
        return $this;
    }
}
