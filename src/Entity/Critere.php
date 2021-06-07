<?php

namespace App\Entity;

use App\Repository\CritereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CritereRepository::class)
 */
class Critere {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @var string
     * @ORM\Column(name="libelle", type="string", length=100, nullable=false)
     * @Assert\NotNull(message="Entrez le nom du critère s'il vous plait")
     */
    private $libelle;

    /**
     *
     * @var boolean
     * @ORM\Column(name="etat", type="boolean", nullable=true)
     */
    private $etat = true;

    /**
     *
     * @var DomaineImpact
     * @ORM\ManyToOne(targetEntity="DomaineImpact", inversedBy="critere")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="domaine_id", referencedColumnName="id")
     * })
     * @Assert\NotNull(message="Choisissez un domaine s'il vous plait")
     */
    public $domaine;

    /**
     * @var Cartographie
     */
    private $cartographie;

    /**
     * @var Critere
     */
    private $newCritere;

    /**
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="GrilleImpact", orphanRemoval=true, mappedBy="critere", cascade={"persist", "remove", "merge"})
     * @Assert\Valid()
     */
    private $grilleImpact;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Impact", mappedBy="critere", cascade={"persist", "remove", "merge"})
     */
    private $impact;

    public function __construct() {
        $this->grilleImpact = new ArrayCollection();
    }

    /**
     * initialize critere
     *
     * @param Cartographie $cartographie
     * @param integer $number
     */
    public function init($cartographie, $number) {
        for($i = 1; $i <= $number; $i ++) {
            $grilleImpact = new GrilleImpact();
            $grille = new Grille();
            $grille->setCartographie($cartographie);
            $grille->setTypeGrille($cartographie->getTypeGrilleImpact());
            $grilleImpact->setGrille($grille);
            $this->grilleImpact->add($grilleImpact);
        }
        $this->cartographie = $cartographie;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle() {
        return $this->libelle;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     * @return Critere
     */
    public function setLibelle($libelle) {
        $this->libelle = $libelle;
        return $this;
    }

    /**
     * Get etat
     *
     * @return boolean
     */
    public function getEtat() {
        return $this->etat;
    }

    /**
     * Set etat
     *
     * @param boolean $etat
     * @return Critere
     */
    public function setEtat($etat) {
        $this->etat = $etat;
        return $this;
    }

    /**
     * Get Domaine
     *
     * @return Domaine
     */
    public function getDomaine() {
        return $this->domaine;
    }

    /**
     * Set domaine
     *
     * @param Domaine $domaine
     * @return Critere
     */
    public function setDomaine($domaine) {
        $this->domaine = $domaine;
        return $this;
    }

    /**
     * @param Cartographie $cartographie
     * @return Controle
     */
    public function setCartographie($cartographie) {
        $this->cartographie = $cartographie;
        return $this;
    }

    /**
     * get cartographie
     * @return Cartographie
     */
    public function getCartographie() {
        return $this->cartographie ? $this->cartographie : ($this->domaine ? $this->domaine->getCartographie() : null);
    }

    /**
     * @param GrilleImpact $grilleImpact
     * @return Critere
     */
    public function addGrilleImpact($grilleImpact) {
        $grilleImpact->setCritere($this);
        $this->grilleImpact->add($grilleImpact);
        return $this;
    }

    /**
     * Get Impact's grille
     *
     * @return ArrayCollection
     */
    public function getGrilleImpact() {
        return $this->grilleImpact;
    }

    /**
     * Get Impact's grille
     *
     * @return GrilleImpact
     */
    public function getGrilleImpactByValue($valeur) {
        return $this->grilleImpact->filter ( function ($grilleImpact) use($valeur) {
            return $grilleImpact->getEtat () && $grilleImpact->getNote () && $grilleImpact->getNote ()->getValeur () == $valeur;
        } );
    }

    /**
     * Remove Impact's grille
     *
     * @param GrilleImpact $grilleImpact
     * @return Critere
     */
    public function removeGrilleImpact($grilleImpact) {
        $this->grilleImpact->removeElement($grilleImpact);
        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function __toString() {
        return $this->libelle;
    }

    /**
     * Add impact
     *
     * @param Impact $impact
     * @return Critere
     */
    public function addImpact(Impact $impact)
    {
        $this->impact[] = $impact;

        return $this;
    }

    /**
     * Remove impact
     *
     * @param Impact $impact
     */
    public function removeImpact(Impact $impact)
    {
        $this->impact->removeElement($impact);
    }

    /**
     * Get impact
     *
     * @return Collection
     */
    public function getImpact()
    {
        return $this->impact;
    }
}