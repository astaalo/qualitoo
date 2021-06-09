<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Extraction
 * @ORM\Table(name="extraction")
 * @ORM\Entity
 */
class Extraction {
	/**
	 *
	 * @var integer 
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;
	
	/**
	 * @var string 
	 * @ORM\Column(name="code", type="string", length=10, nullable=false)
	 */
	private $code;
	
	/**
	 * @var string 
	 * @ORM\Column(name="libelle", type="string", length=100, nullable=false)
	 */
	private $libelle;
	
	
	/**
	 * Get id
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Get code
	 * @return string
	 */
	public function getCode() {
		return $this->code;
	}
	
	/**
	 * Set code
	 * @param string $code        	
	 * @return Critere
	 */
	public function setCode($code) {
		$this->code = $code;
		return $this;
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
	 * @return Critere
	 */
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}
	
	/**
	 * Get libelle
	 * @return string
	 */
	public function __toString() {
		return $this->libelle;
	}
}
