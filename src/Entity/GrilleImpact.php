<?php

namespace App\Entity;

use App\Repository\GrilleImpactRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=GrilleImpactRepository::class)
 */
class GrilleImpact
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="datetime", nullable=true)
     */
    private $dateCreation;

    /**
     * @var boolean
     *
     * @ORM\Column(name="etat", type="boolean", nullable=true)
     */
    private $etat = true;

    /**
     * @var Critere
     *
     * @ORM\ManyToOne(targetEntity="Critere")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="critere_id", referencedColumnName="id")
     * })
     */
    private $critere;

    /**
     * @var Grille
     * @ORM\ManyToOne(targetEntity="Grille", inversedBy="grilleImpact", cascade={"persist", "merge"})
     * @ORM\JoinColumns({
     * 	@ORM\JoinColumn(name="grille_id", referencedColumnName="id")
     * })
     * @Assert\Valid()
     */
    private $grille;

    /**
     * @var GrilleImpact
     *
     * @ORM\ManyToOne(targetEntity="GrilleImpact")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="origine", referencedColumnName="id")
     * })
     */
    private $origine;

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
     * @return \DateTime
     */
    public function getDateCreation() {
        return $this->dateCreation;
    }

    /**
     * @param \DateTime $dateCreation
     * @return GrilleImpact
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
     * @return GrilleImpact
     */
    public function setEtat($etat) {
        $this->etat = $etat;
        return $this;
    }

    /**
     * @return Grille
     */
    public function getGrille() {
        return $this->grille;
    }

    /**
     * @param Grille $grille
     * @return GrilleImpact
     */
    public function setGrille($grille) {
        $grille->setGrilleImpact($this);
        $this->grille = $grille;
        return $this;
    }

    /**
     * get note
     * @return Note
     */
    public function getNote() {
        return $this->grille ? $this->grille->getNote() : null;
    }

    /**
     * @return Critere
     */
    public function getCritere() {
        return $this->critere;
    }

    /**
     * @param Critere $critere
     * @return GrilleImpact
     */
    public function setCritere($critere) {
        if($critere==null && $this->getCritere()) {
            $this->critere->removeGrilleImpact($this);
        }
        $this->critere = $critere;
        return $this;
    }

    /**
     * @return GrilleImpact
     */
    public function getOrigine() {
        return $this->origine;
    }

    /**
     * @param GrilleImpact $origine
     * @return GrilleImpact
     */
    public function setOrigine($origine) {
        $this->origine = $origine;
        return $this;
    }

    /**
     * get grille's libelle
     * @return string
     */
    public function __toString() {
        return sprintf('%s', $this->grille);
    }

}