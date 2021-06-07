<?php

namespace App\Entity;

use App\Repository\EquipementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EquipementRepository::class)
 */
class Equipement
{
    static $types;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero", type="integer", length=11, nullable=true)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=100, nullable=false)
     * @Assert\NotNull(message="Le libellé est obligatoire")
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=50, nullable=true)
     */
    private $code;

    /**
     * @var boolean
     *
     * @ORM\Column(name="etat", type="boolean", nullable=true)
     */
    private $etat = true;

    /**
     * @var Societe
     *
     * @ORM\ManyToOne(targetEntity="Societe")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="societe_id", referencedColumnName="id")
     * })
     */
    private $societe;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $type ;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle_sans_carspecial", type="string", length=255, nullable=true)
     */
    private $libelleSansCarSpecial;

    /**
     * Get libelle
     * @return string
     */
    public function __toString(){
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
     * Set numero
     *
     * @param integer $numero
     * @return Equipement
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return integer
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     * @return Equipement
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
     * Set code
     *
     * @param string $code
     * @return Equipement
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

    /**
     * Set etat
     *
     * @param boolean $etat
     * @return Equipement
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return boolean
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Equipement
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set societe
     *
     * @param Societe $societe
     * @return Equipement
     */
    public function setSociete(Societe $societe = null)
    {
        $this->societe = $societe;

        return $this;
    }

    /**
     * Get societe
     *
     * @return Societe
     */
    public function getSociete()
    {
        return $this->societe;
    }

    /**
     * Set libelleSansCarSpecial
     *
     * @param string $libelleSansCarSpecial
     *
     * @return Equipement
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
