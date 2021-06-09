<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Critere
 * @ORM\Table(name="tableau_bord")
 * @ORM\Entity(repositoryClass="\App\Repository\TableauBordRepository")
 */
class TableauBord {
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
	 * @var string 
	 * @ORM\Column(name="libelle", type="string", length=100, nullable=false)
	 * @Assert\NotNull(message="Entrez le nom du critère s'il vous plait")
	 */
	private $libelle;
	
	/**
	 *
	 * @var boolean 
	 * @ORM\Column(name="etat", type="boolean", nullable=true)
	 */
	private $etat = true;
	
	
	/**
	 * Get libelle
	 * 
	 * @return string
	 */
	public function __toString() {
		return $this->libelle;
	}
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
	public function getEtat() {
		return $this->etat;
	}
	public function setEtat($etat) {
		$this->etat = $etat;
		return $this;
	}
	
}
