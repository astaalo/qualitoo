<?php

namespace App\Entity;

use App\Repository\TypeGrilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TypeGrilleRepository::class)
 */
class TypeGrille
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="libelle", type="string", length=100, nullable=false)
     */
    private $libelle;

    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=100, nullable=false)
     */
    private $description;

    /**
     * @var boolean
     * @ORM\Column(name="etat", type="boolean", nullable=false)
     */
    private $etat = true;

    /**
     * @var Cartographie
     * @ORM\ManyToOne(targetEntity="Cartographie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cartographie_id", referencedColumnName="id")
     * })
     */
    private $cartographie;

    /**
     * @var TypeEvaluation
     * @ORM\ManyToOne(targetEntity="TypeEvaluation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_evaluation_id", referencedColumnName="id")
     * })
     */
    private $typeEvaluation;

    /**
     * @var ModeFonctionnement
     * @ORM\ManyToOne(targetEntity="ModeFonctionnement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mode_fonctionnement_id", referencedColumnName="id")
     * })
     */
    private $modeFonctionnement;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Grille", mappedBy="typeGrille", cascade={"persist", "merge", "remove"})
     */
    private $grille;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="DomaineImpact", mappedBy="typeGrille", cascade={"persist", "merge", "remove"})
     */
    private $domaine;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Note", mappedBy="typeGrille", cascade={"persist", "merge", "remove"})
     */
    private $note;


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
     * @return TypeGrille
     */
    public function setLibelle($libelle) {
        $this->libelle = $libelle;
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
     * @return TypeGrille
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
     * @return TypeGrille
     */
    public function setEtat($etat) {
        $this->etat = $etat;
        return $this;
    }

    /**
     * Get grille
     * @return ArrayCollection
     */
    public function getGrille() {
        return $this->grille;
    }

    /**
     * add grille
     * @param Grille $grille
     * @return TypeGrille
     */
    public function addGrille($grille) {
        $grille->setTypeGrille($this);
        $this->grille->add($grille);
        return $this;
    }

    /**
     * remove grille
     * @param Grille $grille
     * @return TypeGrille
     */
    public function removeGrille($grille) {
        $this->grille->removeElement($grille);
        return $this;
    }


    /**
     * Get domaine
     * @return ArrayCollection
     */
    public function getDomaine() {
        return $this->domaine;
    }

    /**
     * get cartographie
     * @return Cartographie
     */
    public function getCartographie() {
        return $this->cartographie;
    }

    /**
     * set cartographie
     * @param Cartographie $cartographie
     * @return TypeGrille
     */
    public function setCartographie($cartographie) {
        $this->cartographie = $cartographie;
        return $this;
    }

    /**
     * @return TypeEvaluation
     */
    public function getTypeEvaluation() {
        return $this->typeEvaluation;
    }

    /**
     * @param TypeEvaluation $typeEvaluation
     */
    public function setTypeEvaluation($typeEvaluation) {
        $this->typeEvaluation = $typeEvaluation;
        return $this;
    }

    /**
     * get fonctionnement's mode
     * @return ModeFonctionnement
     */
    public function getModeFonctionnement() {
        return $this->modeFonctionnement;
    }

    /**
     * set fonctionnement's mode
     * @return TypeGrille
     */
    public function setModeFonctionnement($modeFonctionnement) {
        $this->modeFonctionnement = $modeFonctionnement;
        return $this;
    }

    /**
     * Get note
     * @return ArrayCollection
     */
    public function getNote() {
        return $this->note;
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
        $this->domaine = new ArrayCollection();
        $this->note = new ArrayCollection();
    }

    /**
     * Add domaine
     *
     * @param DomaineImpact $domaine
     * @return TypeGrille
     */
    public function addDomaine(DomaineImpact $domaine)
    {
        $this->domaine[] = $domaine;

        return $this;
    }

    /**
     * Remove domaine
     *
     * @param DomaineImpact $domaine
     */
    public function removeDomaine(DomaineImpact $domaine)
    {
        $this->domaine->removeElement($domaine);
    }

    /**
     * Add note
     *
     * @param Note $note
     * @return TypeGrille
     */
    public function addNote(Note $note)
    {
        $this->note[] = $note;

        return $this;
    }

    /**
     * Remove note
     *
     * @param Note $note
     */
    public function removeNote(Note $note)
    {
        $this->note->removeElement($note);
    }
}