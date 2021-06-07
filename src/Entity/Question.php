<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuestionRepository::class)
 */
class Question
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255, nullable=false)
     */
    private $libelle;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position = 0;

    /**
     * @var integer
     * @ORM\Column(name="cotation", type="integer", length=1, nullable=false)
     */
    private $cotation;

    /**
     * @var boolean
     *
     * @ORM\Column(name="etat", type="boolean", nullable=true)
     */
    private $etat;

    private $valueEtat = "Active";


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
     * @return Question
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
     * Set etat
     *
     * @param boolean $etat
     * @return Question
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

    public function getValueEtat() {
        if( $this->etat == false){
            $this->valueEtat  = "Désactive";
        }
        return $this->valueEtat;
    }

    /**
     * Set position
     * @param integer $position
     * @return Question
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get position
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * get cotation
     * @return integer
     */
    public function getCotation() {
        return $this->cotation;
    }

    /**
     * set cotation
     * @param integer $cotation
     * @return Question
     */
    public function setCotation($cotation) {
        $this->cotation = $cotation;
        return $this;
    }

    /**
     * get libelle
     * @return string
     */
    public function __toString() {
        return $this->libelle;
    }
}
