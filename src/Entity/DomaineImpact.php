<?php

namespace App\Entity;

use App\Model\TreeInterface;
use App\Repository\DomaineImpactRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Gedmo\Tree(type="nested")
 * @ORM\Entity(repositoryClass=DomaineImpactRepository::class)
 */
class DomaineImpact extends Tree implements TreeInterface
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
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=45, nullable=false)
     * @Assert\NotNull(message="Le nom du domaine est obligatoire")
     */
    private $libelle;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Critere", mappedBy="domaine", cascade={"persist", "merge", "remove"})
     */
    private $critere;

    /**
     * @var Cartographie
     * @ORM\ManyToOne(targetEntity="Cartographie")
     * @ORM\JoinColumns({
     * 	@ORM\JoinColumn(name="cartographie_id", referencedColumnName="id")
     * })
     */
    private $cartographie;

    /**
     * @var boolean
     *
     * @ORM\Column(name="etat", type="boolean", nullable=true)
     */
    private $etat = true;


    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="DomaineImpact", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="DomaineImpact", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    protected $children;


    /**
     * Get id
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get libelle
     * @return string
     */
    public function getLibelle() {
        return $this->libelle;
    }

    /**
     * Set libelle
     * @param string $libelle
     * @return DomaineImpact
     */
    public function setLibelle($libelle) {
        $this->libelle = $libelle;
        return $this;
    }

    /**
     * Get etat
     * @return boolean
     */
    public function getEtat() {
        return $this->etat;
    }

    /**
     * Set etat
     * @param boolean $etat
     * @return DomaineImpact
     */
    public function setEtat($etat) {
        $this->etat = $etat;
        return $this;
    }

    /**
     * Get critere
     * @return ArrayCollection
     */
    public function getCritere() {
        return $this->critere;
    }

    /**
     * Get active critere
     * @return ArrayCollection
     */
    public function getActiveCritere() {
        return $this->critere->filter(function($critere) {
            return $critere->getEtat();
        });
    }

    public function getTypeGrille() {
        return $this->cartographie ? $this->cartographie->getTypeGrille() : null;
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
     * @return DomaineImpact
     */
    public function setCartographie($cartographie) {
        $this->cartographie = $cartographie;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabelClass() {
        $class  = $this->lvl > 0 ? 'child ' : null;
        $class .= $this->children->count() ? 'parent ' : null;
        return $class;
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
     * Get libelle
     *
     * @return string
     */
    public function __toString()
    {
        return $this->libelle;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->critere = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    /**
     * Add critere
     *
     * @param Critere $critere
     * @return DomaineImpact
     */
    public function addCritere(Critere $critere)
    {
        $this->critere[] = $critere;

        return $this;
    }

    /**
     * Remove critere
     *
     * @param Critere $critere
     */
    public function removeCritere(Critere $critere)
    {
        $this->critere->removeElement($critere);
    }

    /**
     * Add children
     *
     * @param DomaineImpact $children
     * @return DomaineImpact
     */
    public function addChild(DomaineImpact $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param DomaineImpact $children
     */
    public function removeChild(DomaineImpact $children)
    {
        $this->children->removeElement($children);
    }
}