<?php

namespace App\Entity;

use App\Repository\GrilleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=GrilleRepository::class)
 */
class Grille
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @var string
     * @ORM\Column(name="libelle", type="string", length=255, nullable=false)
     * @Assert\NotNull(message="Entrez le libellé s'il vous plait")
     */
    private $libelle;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="datetime", nullable=false)
     */
    private $dateCreation;

    /**
     * @var boolean
     *
     * @ORM\Column(name="etat", type="boolean", nullable=true)
     */
    private $etat = true;

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
     * @var Note
     *
     * @ORM\ManyToOne(targetEntity="Note")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="note_id", referencedColumnName="id")
     * })
     * @Assert\NotNull(message="Choisissez une valeur s'il vous plait")
     */
    private $note;

    /**
     * @var integer
     */
    private $niveauCause;

    /**
     * @var integer
     */
    private $niveauImpact;

    /**
     * @var boolean
     */
    private $modeFonctionnement;

    /**
     * @var Cartographie
     */
    private $cartographie;

    /**
     * @var Grille
     *
     * @ORM\ManyToOne(targetEntity="Grille")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="origine", referencedColumnName="id")
     * })
     */
    private $origine;

    /**
     * @var GrilleImpact
     * @ORM\OneToOne(targetEntity="GrilleImpact", mappedBy="grille", cascade={"persist", "merge", "remove"})
     */
    private $grilleImpact;

    public function __construct() {
        $this->dateCreation = new \DateTime('NOW');
    }


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
     * @return GrilleImpact
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
     * @return Grille
     */
    public function setDateCreation($dateCreation) {
        $this->dateCreation = $dateCreation;
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
     * @return Grille
     */
    public function setEtat($etat) {
        $this->etat = $etat;
        return $this;
    }

    /**
     * @return Note
     */
    public function getNote() {
        return $this->note;
    }

    /**
     * @param Note $note
     * @return Grille
     */
    public function setNote($note) {
        $this->typeGrille = $note ? $note->getTypeGrille() : null;
        $this->note = $note;
        return $this;
    }

    public function setNiveauCause($niveau) {
        if(!$niveau) {
            return $this;
        }
        $typeGrille = $this->cartographie ? $this->cartographie->getTypeGrilleCause() : null;
        $data = $typeGrille->getNote()->filter(function($note) use($niveau) {
            return $note->getValeur()==$niveau;
        });
        $this->niveauCause = $niveau;
        $this->setNote($data->count() ? $data->first() : null);
        return $this;
    }

    public function getNiveauCause() {
        return $this->niveauCause ? $this->niveauCause : ($this->note ? $this->note->getValeur() : null);
    }

    public function setNiveauImpact($niveau) {
        if(!$niveau) {
            return $this;
        }
        //$typeGrille = $this->cartographie ? $this->cartographie->getTypeGrilleImpact() : null;
        $typeGrille = $this->typeGrille ? $this->typeGrille : null;
        $data = $typeGrille->getNote()->filter(function($note) use($niveau) {
            return $note->getValeur()==$niveau;
        });
        $this->niveauImpact = $niveau;
        $this->setNote($data->count() ? $data->first() : null);
        return $this;
    }

    public function getNiveauImpact() {
        return $this->niveauImpact ? $this->niveauImpact : ($this->note ? $this->note->getValeur() : null);
    }

    /**
     * @return GrilleImpact
     */
    public function getGrilleImpact() {
        return $this->grilleImpact;
    }

    /**
     * @param GrilleImpact $grilleImpact
     * @return Grille
     */
    public function setGrilleImpact($grilleImpact) {
        $this->grilleImpact = $grilleImpact;
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
     * @return Grille
     */
    public function setTypeGrille($typeGrille) {
        $this->typeGrille = $typeGrille;
        return $this;
    }

    /**
     * @return Cartographie
     */
    public function getCartographie() {
        return $this->cartographie ? $this->cartographie : ($this->grilleImpact ? $this->grilleImpact->getCritere()->getCartographie() : null);
    }

    /**
     * @param Cartographie $cartographie
     * @return TypeGrille
     */
    public function setCartographie($cartographie) {
        $this->cartographie = $cartographie;
        return $this;
    }

    /**
     * @return Grille
     */
    public function getOrigine() {
        return $this->origine;
    }

    /**
     * @param Grille $origine
     * @return Grille
     */
    public function setOrigine($origine) {
        $this->origine = $origine;
        return $this;
    }

    /**
     * get valeur
     * @return integer
     */
    public function getValeur() {
        return $this->note ? $this->note->getValeur() : null;
    }

    /**
     * @return boolean
     */
    public function getModeFonctionnement() {
        return $this->modeFonctionnement;
    }

    /**
     * @param boolean $modeFonctionnement
     * @return Grille
     */
    public function setModeFonctionnement($modeFonctionnement) {
        $this->modeFonctionnement = $modeFonctionnement;
        return $this;
    }

    /**
     * Get libelle
     * @return string
     */
    public function __toString() {
        return $this->libelle;
    }
}
