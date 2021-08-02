<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeProcessus
 *
 * @ORM\Table(name="type_processus")
 * @ORM\Entity
 */
class TypeProcessus
{
	
	static $ids = [
        'macro' => 1,
        'normal' => 2,
        'sous' => 3
    ];
	
    /**
     * @var integer
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
     * @ORM\OneToMany(targetEntity="App\Entity\processus", mappedBy="typeProcessus")
     */
    protected $processus;
    
    
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
	 * @return TypeProcessus
	 */
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getProcessus() {
		return $this->processus;
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
        $this->processus = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add processus
     *
     * @param processus $processus
     * @return TypeProcessus
     */
    public function addProcessus(processus $processus)
    {
        $this->processus[] = $processus;
    
        return $this;
    }

    /**
     * Remove processus
     *
     * @param processus $processus
     */
    public function removeProcessus(processus $processus)
    {
        $this->processus->removeElement($processus);
    }
}
