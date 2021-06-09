<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Manifestation
 * @ORM\Table(name="manifestation")
 * @ORM\Entity
 */
class Manifestation
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
     * @ORM\Column(name="libelle", type="text", nullable=false)
     */
    private $libelle;
    
    /**
     * @var string
     *
     * @ORM\Column(name="libelle_sans_carspecial",  type="string", length=255, nullable=true)
     */
    private $libelleSansCarSpecial;
    

    /**
     * Get id
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
     * @return Manifestation
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
    
    public function __toString(){
    	return $this->libelle;
    }

    /**
     * Set libelleSansCarSpecial
     *
     * @param string $libelleSansCarSpecial
     *
     * @return Manifestation
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
