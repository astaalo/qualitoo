<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="type_colonne")
 * @ORM\Entity
 */
class TypeColonne {
	
	/**
     * @var integer
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
     * @var string
     * @ORM\Column(name="colonne_debut", type="string", length=2, nullable=false)
     */
    private $colonneDebut;
    
    /**
     * @var string
     * @ORM\Column(name="nombre_colonne", type="integer", nullable=false)
     */
    private $nombreColonne;

    
    /**
     * get id
     * return integer
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
     * @return Colonne
     */
    public function setLibelle($libelle) {
    	$this->libelle = $libelle;
    	return $this;
    }
    
    /**
     * get colonne debut
     * @return string
     */
    public function getColonneDebut() {
    	return $this->colonneDebut;
    }
    
    /**
     * set colonne debut
     * @param string $colonneDebut
     * @return TypeColonne
     */
    public function setColonneDebut($colonneDebut) {
    	$this->colonneDebut = $colonneDebut;
    	return $this;
    }
    
    /**
     * get nombre de colonne
     * @return integer
     */
    public function getNombreColonne() {
    	return $this->nombreColonne;
    }
    
    /**
     * set nombre de colonnes
     * @param integer $nombreColonne
     * @return TypeColonne
     */
    public function setNombreColonne($nombreColonne) {
    	$this->nombreColonne = $nombreColonne;
    	return $this;
    }
	
    /**
     * get libelle
     * @return string
     */
    public function __toString() {
    	return $this->libelle;
    }
}
