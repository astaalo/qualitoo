<?php
/*
 * modified by @mariteuw
 */
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Domaine
 * @ORM\Table(name="mode_fonctionnement")
 * @ORM\Entity
 */
class ModeFonctionnement 
{
	/**
	 * @var array
	 */
	static $ids;
	
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
     * @ORM\Column(name="code", type="string", length=5, nullable=false)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=45, nullable=false)
     */
    private $libelle;
    
    
    public function getId() {
    	return $this->id;
    }
    
    /**
     * get code
     * @return string
     */
    public function getCode() {
    	return $this->code;
    }
    
    /**
     * get libelle
     * @return string
     */
    public function __toString() {
    	return $this->libelle;
    }


    /**
     * Set code
     *
     * @param string $code
     * @return ModeFonctionnement
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     * @return ModeFonctionnement
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    
        return $this;
    }

    /**
     * Get libelle
     *
     * @return string 
     */
    public function getLibelle()
    {
        return $this->libelle;
    }
}
