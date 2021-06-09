<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MethodeControle
 *
 * @ORM\Table(name="methode_controle")
 * @ORM\Entity
 */
class MethodeControle
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
     */
    private $libelle;
    
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
	
	
	/**
	 * Get libelle
	 *
	 * @return string
	 */
	public function __toString(){
			
		return $this->libelle;
	}

}
