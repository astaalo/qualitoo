<?php

namespace App\Entity;

use App\Repository\CartographieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CartographieRepository::class)
 */
class Cartographie
{
    static $ids;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=50, nullable=false)
     * @Assert\NotNull(message="Le nom du risque est obligatoire")
     */
    private $libelle;

    /**
     * @var String
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="DomaineImpact", mappedBy="cartographie", cascade={"persist", "merge", "remove"})
     * @ORM\OrderBy({"root" = "asc", "lft" = "asc"})
     */
    private $domaine;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="TypeGrille", mappedBy="cartographie", cascade={"persist", "merge", "remove"})
     */
    private $typeGrille;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="Menace", mappedBy="cartographie")
     */
    private $menace;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Risque", mappedBy="cartographie")
     */
    private $risque;


    public function __construct() {
        $this->typeGrille = new ArrayCollection();
        $this->domaine = new ArrayCollection();
        $this->menace = new ArrayCollection();
    }

    /**
     * get id
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * get libelle
     * @return string
     */
    public function getLibelle() {
        return $this->libelle;
    }

    /**
     * set libelle
     * @param string $libelle
     * @return Activite
     */
    public function setLibelle($libelle) {
        $this->libelle = $libelle;
        return $this;
    }

    /**
     * get description
     * @return String
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * set description
     * @param string $description
     * @return Activite
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * get collection of domaine
     * @return ArrayCollection
     */
    public function getDomaine() {
        return $this->domaine;
    }

    /**
     * get collection of grille's type
     * @return ArrayCollection
     */
    public function getTypeGrille() {
        return $this->typeGrille;
    }

    /**
     * get collection of grille impact's type
     * @return TypeGrille
     */
    public function getTypeGrilleImpact() {
        $data = $this->typeGrille->filter(function($typeGrille) {
            return $typeGrille->getTypeEvaluation()->getId()==TypeEvaluation::$ids['impact'];
        });
        return $data->count() ? $data->first() : null;
    }

    /**
     * get collection of grille cause type
     * @return ArrayCollection
     */
    public function getTypeGrilleCause() {
        $data = $this->typeGrille->filter(function($typeGrille) {
            return $typeGrille->getTypeEvaluation()->getId()==TypeEvaluation::$ids['cause'];
        });
        return $data->count() ? $data->first() : null;
    }

    /**
     * get menace
     * @return Collection
     */
    public function getMenace() {
        return $this->menace;
    }

    /**
     * Get libelle
     * @return string
     */
    public function __toString(){
        return $this->libelle;
    }



    /**
     * Add domaine
     *
     * @param DomaineImpact $domaine
     * @return Cartographie
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
     * Add typeGrille
     *
     * @param TypeGrille $typeGrille
     * @return Cartographie
     */
    public function addTypeGrille(TypeGrille $typeGrille)
    {
        $this->typeGrille[] = $typeGrille;

        return $this;
    }

    /**
     * Remove typeGrille
     *
     * @param TypeGrille $typeGrille
     */
    public function removeTypeGrille(TypeGrille $typeGrille)
    {
        $this->typeGrille->removeElement($typeGrille);
    }

    /**
     * Add menace
     *
     * @param Menace $menace
     * @return Cartographie
     */
    public function addMenace(Menace $menace)
    {
        $this->menace[] = $menace;

        return $this;
    }

    /**
     * Remove menace
     *
     * @param Menace $menace
     */
    public function removeMenace(Menace $menace)
    {
        $this->menace->removeElement($menace);
    }

    /**
     * Add risque
     *
     * @param Risque $risque
     * @return Cartographie
     */
    public function addRisque(Risque $risque)
    {
        $this->risque[] = $risque;

        return $this;
    }

    /**
     * Remove risque
     *
     * @param Risque $risque
     */
    public function removeRisque(Risque $risque)
    {
        $this->risque->removeElement($risque);
    }

    /**
     * Get risque
     *
     * @return Collection
     */
    public function getRisque()
    {
        return $this->risque;
    }
}
