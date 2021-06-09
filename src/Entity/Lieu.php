<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Lieu
 *
 * @ORM\Table(name="lieu")
 * @ORM\Entity
 */
class Lieu
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
     * @var Cartographie
     * @ORM\ManyToOne(targetEntity="Cartographie")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="cartographie_id", referencedColumnName="id")
     * })
     */
    private $cartographie;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="libelle_sans_carspecial", type="string", length=255, nullable=true)
     */
    private $libelleSansCarSpecial;

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
     * @return Lieu
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
     * Set cartographie
     *
     * @param Cartographie $cartographie
     * @return Lieu
     */
    public function setCartographie(Cartographie $cartographie = null)
    {
        $this->cartographie = $cartographie;
    
        return $this;
    }

    /**
     * Get cartographie
     *
     * @return Cartographie
     */
    public function getCartographie()
    {
        return $this->cartographie;
    }

    /**
     * Set libelleSansCarSpecial
     *
     * @param string $libelleSansCarSpecial
     *
     * @return Lieu
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
