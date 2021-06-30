<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RisqueHasImpact
 *
 * @ORM\Table(name="risque_has_impact")
 * @ORM\Entity(repositoryClass="App\Repository\RisqueHasImpactRepository")
 */
class RisqueHasImpact
{
    /**
     * @var Risque
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
	private $id;
	
    /**
     * @var Risque
     * @ORM\ManyToOne(targetEntity="Risque")
     * @ORM\JoinColumn(name="risque_id", referencedColumnName="id")
     * @Assert\NotNull(message="Le choix du risque est obligatoire")
     */
    private $risque;
	
    /**
     * @var Impact
     * @ORM\ManyToOne(targetEntity="Impact", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="impact_id", referencedColumnName="id")
     * @Assert\NotNull(message="Le champ impact est obligatoire")
     * @Assert\Valid
     */
    private $impact;

    /**
     * @var Grille
     * @ORM\ManyToOne(targetEntity="Grille")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="grille_id", referencedColumnName="id")
     * })
     */
    private $grille;
    
    /**
     * @var DomaineImpact
     */
    private $domaine;
    
    /**
     * @return Risque
     */
	public function getRisque() {
		return $this->risque;
	}
	
	/**
	 * @param Risque $risque
	 * @return RisqueHasImpact
	 */
	public function setRisque($risque) {
		$this->risque = $risque;
		return $this;
	}
	
	/**
	 * @return Impact
	 */
	public function getImpact() {
		return $this->impact;
	}
	
	/**
	 * @param Impact $impact
	 * @return RisqueHasImpact
	 */
	public function setImpact($impact) {
		$this->impact = $impact;
		return $this;
	}
	
	/**
	 * @return Grille
	 */
	public function getGrille() {
		return $this->grille;
	}
	
	/**
	 * @param Grille $grille
	 * @return RisqueHasImpact
	 */
	public function setGrille($grille) {
		$this->grille = $grille;
		return $this;
	}
	
	/**
	 * @return DomaineImpact
	 */
	public function getDomaine() {
		return $this->domaine 
			? $this->domaine 
			: ($this->impact ? ($this->impact->getCritere() ? $this->impact->getCritere()->getDomaine() : null) : null);
	}
	
	/**
	 * set domaine
	 * @param Domaine $domaine
	 * @return RisqueHasImpact
	 */
	public function setDomaine($domaine) {
		$this->domaine = $domaine;
		return $this;
	}
	
	public function getId() {
		return $this->id;
	}
	
	
}
