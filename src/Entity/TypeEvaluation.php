<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeEvaluation
 *
 * @ORM\Table(name="type_evaluation")
 * @ORM\Entity
 */
class TypeEvaluation
{
	static $ids = [
        'cause' => 1,
        'impact' => 2,
        'maitrise' => 3,
        'maturite' => 4
    ];
	
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
     * @ORM\Column(name="libelle", type="string", length=25, nullable=false)
     */
    private $libelle;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="TypeGrille", mappedBy="typeEvaluation", cascade={"persist", "merge", "remove"})
     */
    private $typeGrille;
    
    
    /**
     * @return number
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
	 * @return TypeTypeTypeGrille
	 */
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}
	
	/**
	 * Get typeGrille
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getTypeGrille() {
		return $this->typeGrille;
	}

	/**
	 * Get libelle
	 * @return string
	 */
	public function __toString() {
		return $this->libelle;
	}
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->typeGrille = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add typeGrille
     *
     * @param TypeGrille $typeGrille
     * @return TypeEvaluation
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
}
