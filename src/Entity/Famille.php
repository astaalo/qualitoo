<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use App\Model\TreeInterface;


/**
 * Famille

 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="famille")
 * @ORM\Entity
 */
class Famille extends Tree implements TreeInterface
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
     * @var boolean
     *
     * @ORM\Column(name="etat", type="boolean", nullable=true)
     */
    private $etat = true;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Societe", mappedBy="famille")
     */
    private $societe;
	
    /**
     * @var String
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;
    
    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Famille", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Structure", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    protected $children;
    
    /**
     * @var \Doctrine\Common
     * @ORM\OneToMany(targetEntity="Cause", mappedBy="famille")
     */
    protected $cause;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->societe = new \Doctrine\Common\Collections\ArrayCollection();
    }
	
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
	 * @return Famille
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
	 * @return Famille
	 */
	public function setEtat($etat) {
		$this->etat = $etat;
		return $this;
	}
	
	/**
	 * Get description
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * set description
	 * @param string $description
	 * @return Famille
	 */
	public function setDescription($description) {
		$this->description = $description;
		return $this;
	}
	
	/**
	 * Get causes
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getCause() {
		return $this->cause;
	}
	
	/**
	 * Get societes
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getSociete() {
		return $this->societe;
	}
	
	/**
	 * Add societe
	 * @param Societe $societe
	 * @return Famille
	 */
	public function addSociete(Societe $societe)
	{
		$this->societe[] = $societe;
		return $this;
	}
	
	/**
	 * Remove societe
	 * @param Societe $societe
	 */
	public function removeSociete(Societe $societe)
	{
		$this->societe->removeElement($societe);
	}
	
	/**
	 * Get libelle
	 * @return string
	 */
	public function __toString()
	{
		return $this->libelle;
	}
	


    /**
     * Add children
     *
     * @param Structure $children
     * @return Famille
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
     * Add cause
     *
     * @param Cause $cause
     * @return Famille
     */
    public function addCause(Cause $cause)
    {
        $this->cause[] = $cause;
    
        return $this;
    }

    /**
     * Remove cause
     *
     * @param Cause $cause
     */
    public function removeCause(Cause $cause)
    {
        $this->cause->removeElement($cause);
    }
}
