<?php

namespace App\Entity;

use App\Repository\TypeDocumentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TypeDocumentRepository::class)
 */
class TypeDocument
{

    const TYPE_TDB			= 'TYPE_TDB';
    const TYPE_VEILLE		= 'TYPE_VEILLE';

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
     * @ORM\Column(name="libelle", type="string", length=100, nullable=true)
     * @Assert\NotNull(message="Le libellé est obligatoire")
     */
    private $libelle;

    /**
     * @var string
     * @ORM\Column(name="code", type="string", length=15, nullable=true)
     */
    private $code;


    /**
     * @var String
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;
    public function getId() {
        return $this->id;
    }
    public function getLibelle() {
        return $this->libelle;
    }
    public function setLibelle($libelle) {
        $this->libelle = $libelle;
        return $this;
    }
    public function getDescription() {
        return $this->description;
    }
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }
    public function __toString() {
        return $this->libelle;
    }


    /**
     * Set code
     *
     * @param string $code
     * @return TypeDocument
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
