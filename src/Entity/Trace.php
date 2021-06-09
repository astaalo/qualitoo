<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use App\Model\TreeInterface;

/**
 * Trace
 *
 * @ORM\Table(name="trace")
 * @ORM\Entity
 */
class Trace
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
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \TypeTrace
     *
     * @ORM\ManyToOne(targetEntity="TypeTrace")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_trace_id", referencedColumnName="id")
     * })
     */
    private $typeTrace;

    /**
     * @var \Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
     * })
     */
    private $utilisateur;
	public function getId() {
		return $this->id;
	}
	

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="etat", type="boolean", nullable=true)
	 */
	private $etat = '1';
	
	public function getDescription() {
		return $this->description;
	}
	
	public function setDescription($description) {
		$this->description = $description;
		return $this;
	}
	
	public function getTypeTrace() {
		return $this->typeTrace;
	}
	
	public function setTypeTrace($typeTrace) {
		$this->typeTrace = $typeTrace;
		return $this;
	}
	
	public function getUtilisateur() {
		return $this->utilisateur;
	}
	
	public function setUtilisateur($utilisateur) {
		$this->utilisateur = $utilisateur;
		return $this;
	}
	/**
	 * Get descrption
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->description;
	}
	public function getEtat() {
		return $this->etat;
	}
	public function setEtat($etat) {
		$this->etat = $etat;
		return $this;
	}
	
	


}
