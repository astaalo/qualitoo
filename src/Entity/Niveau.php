<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Niveau
 * 
 * @ORM\Table(name="niveau")
 * @ORM\Entity
 */
class Niveau
{
    /**
     * @var integer
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
	 * @return Niveau
	 */
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
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
