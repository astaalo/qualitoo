<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NoteRepository::class)
 */
class Note
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
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="datetime", nullable=false)
     */
    private $dateCreation;

    /**
     * @var integer
     *
     * @ORM\Column(name="valeur", type="integer", length=1, nullable=false)
     */
    private $valeur;

    /**
     * @var boolean
     *
     * @ORM\Column(name="etat", type="boolean", nullable=false)
     */
    private $etat = true;

    /**
     * @var Note
     *
     * @ORM\ManyToOne(targetEntity="Note")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="origine", referencedColumnName="id")
     * })
     */
    private $origine;

    /**
     * @var TypeGrille
     *
     * @ORM\ManyToOne(targetEntity="TypeGrille")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_grille_id", referencedColumnName="id")
     * })
     */
    private $typeGrille;


    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Grille", mappedBy="note")
     */
    private $grille;


    /**
     * @return number
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLibelle() {
        return $this->libelle;
    }

    /**
     * @param string $libelle
     * @return Note
     */
    public function setLibelle($libelle) {
        $this->libelle = $libelle;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreation() {
        return $this->dateCreation;
    }

    /**
     * @param \DateTime $dateCreation
     * @return Note
     */
    public function setDateCreation(\DateTime $dateCreation) {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    /**
     * @return integer
     */
    public function getValeur() {
        return $this->valeur;
    }

    /**
     * @param integer $valeur
     * @return Note
     */
    public function setValeur($valeur) {
        $this->valeur = $valeur;
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
     * @return Note
     */
    public function setEtat($etat) {
        $this->etat = $etat;
        return $this;
    }

    /**
     * @return Note
     */
    public function getOrigine() {
        return $this->origine;
    }

    /**
     * @param Note $origine
     * @return Note
     */
    public function setOrigine($origine) {
        $this->origine = $origine;
        return $this;
    }

    /**
     * @return TypeGrille
     */
    public function getTypeGrille() {
        return $this->typeGrille;
    }

    /**
     * @param TypeGrille $typeGrille
     * @return Note
     */
    public function setTypeGrille($typeGrille) {
        $this->typeGrille = $typeGrille;
        return $this;
    }

    /**
     * @param ArrayCollection
     */
    public function getGrille() {
        return $this->grille;
    }

    /**
     * @param ArrayCollection
     */
    public function getActiveGrille() {
        return $this->grille->filter(function($grille) {
            return $grille->getEtat();
        });
    }

    /**
     * Get libelle
     * @return string
     */
    public function __toString() {
        return $this->libelle;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->grille = new ArrayCollection();
    }

    /**
     * Add grille
     *
     * @param Grille $grille
     * @return Note
     */
    public function addGrille(Grille $grille)
    {
        $this->grille[] = $grille;

        return $this;
    }

    /**
     * Remove grille
     *
     * @param Grille $grille
     */
    public function removeGrille(Grille $grille)
    {
        $this->grille->removeElement($grille);
    }
}