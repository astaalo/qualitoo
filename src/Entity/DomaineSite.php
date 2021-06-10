<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\DomaineSiteRepository;

/**
 * Domaine
 * @ORM\Table(name="domaine_site")
 * @ORM\Entity(repositoryClass=DomaineSiteRepository::class)
 */
class DomaineSite 
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
     * @Assert\NotNull(message="Le nom du domaine est obligatoire")
     */
    private $libelle;
   
    /**
     * @var boolean
     * @ORM\Column(name="etat", type="boolean", nullable=true)
     */
    private $etat = true;
    
    /**
     * @var string
     *
     * @ORM\Column(name="libelle_sans_carspecial", type="string", length=255, nullable=true)
     */
    private $libelleSansCarSpecial;
    
    /**
     * get id
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
     * @return DomaineSite
     */
    public function setLibelle($libelle) {
    	$this->libelle = $libelle;
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
     * @return DomaineSite
     */
    public function setEtat($etat) {
    	$this->etat = $etat;
    	return $this;
    }
    
    /**
     * get libelle
     * @return string
     */
    public function __toString() {
    	return $this->libelle;
    }

    /**
     * Set libelleSansCarSpecial
     *
     * @param string $libelleSansCarSpecial
     * @return DomaineSite
     */
    public function setLibelleSansCarSpecial($libelleSansCarSpecial)
    {
        $this->libelleSansCarSpecial = $libelleSansCarSpecial;
    
        return $this;
    }

    /**
     * Get libelleSansCarSpecial
     *
     * @return string 
     */
    public function getLibelleSansCarSpecial()
    {
        return $this->libelleSansCarSpecial;
    }
}
