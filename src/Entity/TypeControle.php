<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeControle
 *
 * @ORM\Table(name="type_controle")
 * @ORM\Entity
 */
class TypeControle
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
     */
    private $libelle;
    
    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Controle", mappedBy="controle_id")
     */
    protected $controle;
    
    
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
	public function getControle() {
		return $this->controle;
	}
	public function setControle($controle) {
		$this->controle = $controle;
		return $this;
	}
	
	/**
	 * Get libelle
	 *
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
        $this->controle = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add controle
     *
     * @param Controle $controle
     * @return TypeControle
     */
    public function addControle(Controle $controle)
    {
        $this->controle[] = $controle;
    
        return $this;
    }

    /**
     * Remove controle
     *
     * @param Controle $controle
     */
    public function removeControle(Controle $controle)
    {
        $this->controle->removeElement($controle);
    }
}
