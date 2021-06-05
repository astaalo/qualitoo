<?php

namespace App\Entity;

use App\Repository\StructureRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use App\Model\TreeInterface;

/**
 * Structure
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="structure")
 * @ORM\Entity(repositoryClass=StructureRepository::class)
 */
class Structure extends Tree implements TreeInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="code", type="string", length=100, nullable=false)
     * @Assert\NotNull(message="Le nom de la structure est obligatoire")
     */
    private $code;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="name_sans_spec_char", type="string", length=255, nullable=false)
     */
    private $nameSansSpecChar;

    /**
     * @var string
     * @ORM\Column(name="libelle", type="string", length=100, nullable=false)
     * @Assert\NotNull(message="Le nom complet de la structure est obligatoire")
     */
    private $libelle;

    /**
     * @var \DateTime
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
     * @var TypeStructure
     *
     * @ORM\ManyToOne(targetEntity="TypeStructure")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_structure_id", referencedColumnName="id")
     * })
     * @Assert\NotNull(message="Le type de structure est obligatoire")
     */
    private $typeStructure;

    /**
     * @var Societe
     *
     * @ORM\ManyToOne(targetEntity="Societe")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="societe_id", referencedColumnName="id")
     * })
     */
    private $societe;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Structure", inversedBy="children", cascade={"persist", "merge","remove"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Structure", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    protected $children;

    /**
     * @ORM\OneToMany(targetEntity="Utilisateur", mappedBy="structure")
     */
    protected $utilisateur;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Utilisateur", mappedBy="structureOfConsulteur", cascade={"persist","remove","merge"})
     */
    protected $consulteur;

    public function __construct() {
        $this->dateCreation = new \DateTime('NOW');
    }

    /**
     * get Id
     * @return number
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @param string $code
     * @return Structure
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
     * @return Structure
     */
    public function setLibelle($libelle) {
        $this->nameSansSpecChar = $this->removeSpecialChar($libelle);
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
     * @return Structure
     */
    public function setEtat($etat) {
        $this->etat = $etat;
        return $this;
    }

    /**
     * @return TypeStructure
     */
    public function getTypeStructure() {
        return $this->typeStructure;
    }

    /**
     * @param TypeStructure $typeStructure
     * @return Structure
     */
    public function setTypeStructure($typeStructure) {
        $this->typeStructure = $typeStructure;
        return $this;
    }

    /**
     * @return Societe
     */
    public function getSociete() {
        return $this->societe;
    }

    /**
     * @param Societe $societe
     * @return Structure
     */
    public function setSociete($societe) {
        $this->societe = $societe;
        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Get name
     * @return string
     */
    public function getName() {
        $object = $this;
        $code = null;
        if($object->getLvl() != 0) {
            if($object->getParent() == null) {
            } else {
                $code = $object->getParent()->getName().' \ '.$object->getCode().$code;
            }
        } else {
            $code = $object->getCode();
        }
        return $code;
    }

    /**
     * @param string $name
     */
    public function setName($name){
        $this->name=$name;
        return $this;
    }
    /**
     * @return Utilisateur
     */
    public function getManager() {
        foreach($this->utilisateur as $utilisateur) {
            if($utilisateur->isManager()) {
                return $utilisateur;
            }
        }
        return null;
    }

    public function getDirection(){
        if($this->parent) {
            return $this->parent->getDirection();
        } else {
            return $this;
        }
    }


    /**
     * Set nameSansSpecChar
     *
     * @param string $nameSansSpecChar
     * @return Structure
     */
    public function setNameSansSpecChar($nameSansSpecChar)
    {
        $this->nameSansSpecChar = $nameSansSpecChar;

        return $this;
    }

    /**
     * Get nameSansSpecChar
     *
     * @return string
     */
    public function getNameSansSpecChar()
    {
        return $this->nameSansSpecChar;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Structure
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Add children
     *
     * @param Structure $children
     * @return Structure
     */
    public function addChild(Structure $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param Structure $children
     */
    public function removeChild(Structure $children)
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

    /**
     * Add utilisateur
     *
     * @param Utilisateur $utilisateur
     * @return Structure
     */
    public function addUtilisateur(Utilisateur $utilisateur)
    {
        $this->utilisateur[] = $utilisateur;

        return $this;
    }

    /**
     * Remove utilisateur
     * @param Utilisateur $utilisateur
     */
    public function removeUtilisateur(Utilisateur $utilisateur)
    {
        $this->utilisateur->removeElement($utilisateur);
    }

    /**
     * Get utilisateur
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Add consulteur
     * @param Utilisateur $consulteur
     * @return Structure
     */
    public function addConsulteur(Utilisateur $consulteur) {
        $this->consulteur[] = $consulteur;
        return $this;
    }

    /**
     * Remove consulteur
     * @param Utilisateur $consulteur
     */
    public function removeConsulteur(Utilisateur $consulteur) {
        $this->consulteur->removeElement($consulteur);
    }

    /**
     * Get consulteur
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConsulteur() {
        return $this->consulteur;
    }
}
