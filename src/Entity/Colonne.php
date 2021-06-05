<?php

namespace App\Entity;

use App\Repository\ColonneRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ColonneRepository::class)
 */
class Colonne {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Societe
     * @ORM\ManyToOne(targetEntity="Societe", inversedBy="colonne")
     * @ORM\JoinColumns({
     * 	@ORM\JoinColumn(name="societe_id", referencedColumnName="id")
     * })
     */
    private $societe;

    /**
     * @var TypeColonne
     * @ORM\ManyToOne(targetEntity="TypeColonne")
     * @ORM\JoinColumns({
     * 	@ORM\JoinColumn(name="type_colonne_id", referencedColumnName="id")
     * })
     */
    private $typeColonne;

    /**
     * @var Extraction
     * @ORM\ManyToOne(targetEntity="Extraction")
     * @ORM\JoinColumns({
     * 	@ORM\JoinColumn(name="extraction_id", referencedColumnName="id")
     * })
     */
    private $extraction;

    /**
     * @var boolean
     * @ORM\Column(name="etat", type="boolean")
     */
    private $etat = true;


    /**
     * get id
     * return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * get societe
     * @return Societe
     */
    public function getSociete() {
        return $this->societe;
    }

    /**
     * set societe
     * @param Societe $societe
     * @return Extraction
     */
    public function setSociete($societe) {
        $this->societe = $societe;
        return $this;
    }

    /**
     * get extraction
     * @return Extraction
     */
    public function getExtraction() {
        return $this->extraction;
    }

    /**
     * set extraction
     * @param Extraction $extraction
     */
    public function setExtraction($extraction) {
        $this->extraction = $extraction;
        return $this;
    }

    /**
     * get typeColonne
     * @return TypeColonne
     */
    public function getTypeColonne() {
        return $this->typeColonne;
    }

    /**
     * set typeColonne
     * @param TypeColonne $typeColonne
     */
    public function setTypeColonne($typeColonne) {
        $this->typeColonne = $typeColonne;
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
     * @return Colonne
     */
    public function setEtat($etat) {
        $this->etat = $etat;
        return $this;
    }
}
