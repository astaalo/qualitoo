<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UniteTemps
 *
 * @ORM\Table(name="unite_temps")
 * @ORM\Entity
 */
class UniteTemps
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
    
    /**
	 * @var string 
	 * @ORM\Column(name="code", type="string", length=25, nullable=true)
	 */
    private $code;
    
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
	 * Get libelle
	 * @return string
	 */
	public function __toString(){
		return $this->libelle;
	}


    /**
     * Set code
     *
     * @param string $code
     * @return Periodicite
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }
}
