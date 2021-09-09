<?php

namespace App\Entity;

use App\Model\TreeInterface;
use App\Repository\ProcessusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProcessusRepository::class)
 */
class Processus
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
     * @ORM\Column(name="libelle", type="string", length=255, nullable=true)
     * @Assert\NotNull(message="Le nom du processus est obligatoire")
     */
    private $libelle;

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
     * @ORM\ManyToOne(targetEntity=Processus::class, inversedBy="processuses")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity=Processus::class, mappedBy="parent")
     */
    private $processuses;

    /**
     * @ORM\ManyToOne(targetEntity=Societe::class, inversedBy="processuses")
     */
    private $societe;

    public function __construct()
    {
        $this->processuses = new ArrayCollection();
    }

    /**
     * @return integer
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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getProcessuses(): Collection
    {
        return $this->processuses;
    }

    public function addProcessus(self $processus): self
    {
        if (!$this->processuses->contains($processus)) {
            $this->processuses[] = $processus;
            $processus->setParent($this);
        }

        return $this;
    }

    public function removeProcessus(self $processus): self
    {
        if ($this->processuses->removeElement($processus)) {
            // set the owning side to null (unless already changed)
            if ($processus->getParent() === $this) {
                $processus->setParent(null);
            }
        }

        return $this;
    }

    public function getSociete(): ?Societe
    {
        return $this->societe;
    }

    public function setSociete(?Societe $societe): self
    {
        $this->societe = $societe;

        return $this;
    }
}
