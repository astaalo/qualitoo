<?php

namespace App\Entity;

use App\Model\TreeInterface;
use App\Repository\ProcessusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProcessusRepository::class)
 */
class Processus extends Tree implements TreeInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
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
     * @ORM\Column(name="libelle", type="string", length=255, nullable=true)
     * @Assert\NotNull(message="Le nom du processus est obligatoire")
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
     * @var Structure
     *
     * @ORM\ManyToOne(targetEntity="Structure")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="structure_id", referencedColumnName="id")
     * })
     * @Assert\NotNull(message="Veuillez choisir la structure ...")
     */
    private $structure;

    /**
     * @var TypeProcessus
     *
     * @ORM\ManyToOne(targetEntity="TypeProcessus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_processus_id", referencedColumnName="id")
     * })
     * @Assert\NotNull(message="Veuillez choisir le type de processus ...")
     */
    private $typeProcessus;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Activite", mappedBy="processus", cascade={"persist", "merge"})
     */
    private $activite;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Projet", mappedBy="processus", cascade={"persist", "merge", "remove"})
     */
    private $projet;

    /**
     * @var Processus
     *
     * @ORM\ManyToOne(targetEntity="Processus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="origine", referencedColumnName="id")
     * })
     */
    private $origine;

    /**
     * @var Processus
     */
    public $processus;

    /**
     * @var ProfilRisque
     */
    public $profilRisque;


    /**
     * @Gedmo\TreeParent()
     * @ORM\ManyToOne(targetEntity="Processus", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Processus", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    protected $children;

    public function __construct() {
        $this->activite = new ArrayCollection();
        $this->projet	= new ArrayCollection();
        $this->risque	= new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return ArrayCollection
     */
    public function getActivite() {
        return $this->activite;
    }

    /**
     * @param Activite $activite
     * @return Processus
     */
    public function addActivite($activite) {
        $activite->setProcessus($this);
        $this->activite->add($activite);
        return $this;
    }

    /**
     * @return integer
     */
    public function getProbabilite() {
        $probabilite = null;
        foreach($this->getRisque() as $risque) {
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
        foreach($this->getRisque() as $risque) {
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
        foreach($this->getRisque() as $risque) {
            if($risque->getProbabilite() && $risque->getGravite()) {
                $icg += $risque->getProbabilite() * $risque->getGravite();
                $number = $number + 1;
            }
        }
        $icg = $icg ? $icg/$number : 0;
        return $icg ? ($icg < 1 ? 1 : round($icg)) : null;
    }

    /**
     * @return ArrayCollection
     */
    public function getProjet() {
        return $this->projet;
    }



    /**
     * @param Projet $projet
     * @return Projet
     */
    public function addProjet($projet) {
        $projet->setProcessus($this);
        $this->projet->add($projet);
        return $this;
    }

    public function getRisque(&$collection = null) {
        if($collection==null) {
            $collection = new ArrayCollection();
        }
        foreach($this->children as $processus) {
            $processus->getRisque($collection);
        }
        foreach($this->activite as $activite) {
            foreach($activite->getRisque() as $risque) {
                $collection->add($risque);
            }
        }
        return $collection;
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
     * @return Processus
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
     * @return Processus
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
     * @return Processus
     */
    public function setEtat($etat) {
        $this->etat = $etat;
        return $this;
    }

    /**
     * @return Structure
     */
    public function getStructure() {
        return $this->structure;
    }

    /**
     * @param Structure $structure
     * @return Processus
     */
    public function setStructure($structure) {
        $this->structure = $structure;
        return $this;
    }

    /**
     * @return TypeProcessus
     */
    public function getTypeProcessus() {
        return $this->typeProcessus;
    }

    /**
     * @param TypeProcessus $typeProcessus
     * @return Processus
     */
    public function setTypeProcessus($typeProcessus) {
        $this->typeProcessus = $typeProcessus;
        return $this;
    }

    /**
     * @return Processus
     */
    public function getOrigine() {
        return $this->origine;
    }

    /**
     * @param Processus $origine
     * @return Processus
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
     * @return Processus
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * show values
     * @return array
     */
    public function showValuesToArray($data = array()) {
        if($this->parent==null) {
            return $data;
        }
        if($this->typeProcessus->getId()==TypeProcessus::$ids['macro']) {
            $data['macro'] = array('id' => $this->id, 'libelle' => $this->libelle);
        }
        if($this->typeProcessus->getId()==TypeProcessus::$ids['normal']) {
            $data['normal'] = array('id' => $this->id, 'libelle' => $this->libelle);
        }
        if($this->typeProcessus->getId()==TypeProcessus::$ids['sous']) {
            $data['sous'] = array('id' => $this->id, 'libelle' => $this->libelle);
        }
        return $this->parent->showValuesToArray($data);
    }

    /**
     * show ids
     * @return array
     */
    public function showIdsToArray($data = array()) {
        if($this->parent==null) {
            return $data;
        }
        if($this->typeProcessus->getId()==TypeProcessus::$ids['macro']) {
            $data['macro'] = array('id' => $this->id);
        }
        if($this->typeProcessus->getId()==TypeProcessus::$ids['normal']) {
            $data['normal'] = array('id' => $this->id);
        }
        if($this->typeProcessus->getId()==TypeProcessus::$ids['sous']) {
            $data['sous'] = array('id' => $this->id);
        }
        return $this->parent->showIdsToArray($data);
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function __toString(){
        return $this->libelle ? $this->libelle : '';
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        $object = $this;
        $libelle = null;
        if($object->getLvl() != 0) {
            $libelle = $object->getParent()->getName().' \ '.$object->getLibelle().$libelle;
        } else {
            $libelle = $object->getLibelle();
        }
        return $libelle;
    }



    /**
     * Set libelleSansCarSpecial
     *
     * @param string $libelleSansCarSpecial
     * @return Processus
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
     * Remove activite
     *
     * @param Activite $activite
     */
    public function removeActivite(Activite $activite)
    {
        $this->activite->removeElement($activite);
    }

    /**
     * Remove projet
     *
     * @param Projet $projet
     */
    public function removeProjet(Projet $projet)
    {
        $this->projet->removeElement($projet);
    }

    /**
     * Add children
     *
     * @param Processus $children
     * @return Processus
     */
    public function addChild(Processus $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param Processus $children
     */
    public function removeChild(Processus $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * @return array
     */
    public function getChildrenIds() {
        $ids= array($this->getId());
        foreach($this->children as $child) {
            $ids = array_merge($ids, $child->getChildrenIds());
        }
        return $ids;
    }
}
