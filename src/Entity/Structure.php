<?php

namespace App\Entity;

use App\Repository\StructureRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Structure
 * @ORM\Table(name="structure")
 * @ORM\Entity(repositoryClass=StructureRepository::class)
 */
class Structure
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
     * @ORM\OneToMany(targetEntity="Utilisateur", mappedBy="structure")
     */
    protected $utilisateur;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $service;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $departement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pole;


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

    public function getService(): ?string
    {
        return $this->service;
    }

    public function setService(?string $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getDepartement(): ?string
    {
        return $this->departement;
    }

    public function setDepartement(?string $departement): self
    {
        $this->departement = $departement;

        return $this;
    }

    public function getPole(): ?string
    {
        return $this->pole;
    }

    public function setPole(?string $pole): self
    {
        $this->pole = $pole;

        return $this;
    }
}
