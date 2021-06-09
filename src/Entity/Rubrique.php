<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Rubrique
 * @ORM\Table(name="rubrique")
 * @ORM\Entity
 */
class Rubrique
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
	 * @ORM\Column(name="libelle", type="string", length=100, nullable=false)
	 * @Assert\NotNull(message="Veuillez saisir le nom de la rubrique")
	 */
	private $libelle;
	

	/**
	 * @var integer
	 * @ORM\Column(name="categorie", type="integer", length=1, nullable=false)
	 * @Assert\NotNull(message="Veuillez choisir la catégorie")
	 */
	private $categorie;
	
	/**
	 * @var string
	 * @ORM\Column(name="description", type="string", length=255,nullable=true)
	 * 
	 */
	private $description;
	
	
	/**
	 * @var boolean
	 */
	private $etat = true;
	
	
    /**
     * toString
     */
    public function __toString()
    {
    	return $this->libelle;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     * @return Audit
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
    
    /**
     * @return number
     */
	public function getCategorie() {
		return $this->categorie;
	}
	
	/**
	 * @param integer $categorie
	 * @return Rubrique
	 */
	public function setCategorie($categorie) {
		$this->categorie = $categorie;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * @param string $description
	 * @return Rubrique
	 */
	public function setDescription($description) {
		$this->description = $description;
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function getEtat() {
		return $this->etat;
	}
	
	/**
	 * @param boolean $etat
	 * @return Rubrique
	 */
	public function setEtat($etat) {
		$this->etat = $etat;
		return $this;
	}

}
