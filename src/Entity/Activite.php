<?php

namespace App\Entity;

use App\Repository\ActiviteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ActiviteRepository::class)
 */
class Activite
{
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
     * @ORM\Column(name="libelle", type="string", length=255, nullable=false)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle_sans_carspecial", type="string", length=255, nullable=true)
     */
    private $libelleSansCarSpecial;

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
     * @var String
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var Processus
     *
     * @ORM\ManyToOne(targetEntity="Processus", inversedBy="activite" )
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="processus_id", referencedColumnName="id")
     * })
     */
    private $processus;

    /**
     * @var Activite
     *
     * @ORM\ManyToOne(targetEntity="Activite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="origine", referencedColumnName="id")
     * })
     */
    private $origine;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="RisqueMetier", mappedBy="activite", cascade={"persist", "merge", "remove"})
     */
    private $risque;

    /**
     * @var Structure
     */
    public $structure;

    /**
     * @var TypeProcessus
     */
    public $typeProcessus;

    /**
     * @var ProfilRisque
     */
    public $profilRisque;


    public function getId() {
    return $this->id;
}

    /**
     * @return integer
     */
    public function getNumero() {
    return $this->numero;
}

    /**
     * @param integer $numero
     * @return Processus
     */
    public function setNumero($numero) {
    $this->numero = $numero;
    return $this;
}

    /**
     * @return string
     */
    public function getCode() {
    return $this->code;
}

    /**
     * @param string $code
     * @return Activite
     */
    public function setCode($code) {
    $this->code = $code;
    return $this;
}

    /**
     * @return string
     */
    public function getLibelle() {
    return $this->libelle;
}

    /**
     * @param string $libelle
     * @return Activite
     */
    public function setLibelle($libelle) {
    $this->libelle = $libelle;
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
     * @return Activite
     */
    public function setEtat($etat) {
    $this->etat = $etat;
    return $this;
}

    /**
     * @return Processus
     */
    public function getProcessus() {
    return $this->processus;
}

    /**
     * @param Processus $processus
     * @return Activite
     */
    public function setProcessus($processus) {
    $this->processus = $processus;
    return $this;
}

    /**
     * @return Activite
     */
    public function getOrigine() {
    return $this->origine;
}

    /**
     * @param Activite $origine
     * @return Activite
     */
    public function setOrigine($origine) {
    $this->origine = $origine;
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
     * @return Activite
     */
    public function setDescription($description) {
    $this->description = $description;
    return $this;
}

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getRisque() {
    return $this->risque;
}

    /**
     * @return integer
     */
    public function getProbabilite() {
    $probabilite = null;
    foreach($this->risque as $rm) {
        $risque=$rm->getRisque();
        if($probabilite) {
            $probabilite = $risque->getProbabilite() ? ($probabilite > $risque->getProbabilite() ? $probabilite : $risque->getProbabilite()) : $probabilite;
        } else {
            $probabilite = $risque->getProbabilite();
        }
    }
    return $probabilite;
}

    /**
     * @return integer
     */
    public function getGravite() {
    $gravite = $number = 0;
    foreach($this->risque as $rm) {
        $risque=$rm->getRisque();
        if($risque->getGravite()) {
            $gravite += $risque->getGravite();
            $number = $number + 1;
        }
    }
    $gravite = $gravite ? $gravite/$number : 0;
    return $gravite ? ($gravite < 1 ? 1 : round($gravite)) : null;
}

    /**
     * @return
     */
    public function getICG() {
    $icg = $number = 0;
    foreach($this->risque as $rm) {
        $risque=$rm->getRisque();
        if($risque->getProbabilite() && $risque->getGravite()) {
            $icg += $risque->getProbabilite() * $risque->getGravite();
            $number = $number + 1;
        }
    }
    $icg = $icg ? $icg/$number : 0;
    return $icg ? ($icg < 1 ? 1 : round($icg)) : null;
}

    /**
     * @param RisqueMetier $risque
     * @return Activite
     */
    public function addRisque($risque) {
    $risque->setActivite($this);
    $this->risque->add($risque);
    return $this;
}

    /**
     * Get libelle
     * @return string
     */
    public function __toString(){
    return $this->libelle;
}


    /**
     * Constructor
     */
    public function __construct()
{
    $this->risque = new \Doctrine\Common\Collections\ArrayCollection();
}

    /**
     * Set libelleSansCarSpecial
     *
     * @param string $libelleSansCarSpecial
     * @return Activite
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

    /**
     * Remove risque
     *
     * @param RisqueMetier $risque
     */
    public function removeRisque(RisqueMetier $risque)
{
    $this->risque->removeElement($risque);
}
}
