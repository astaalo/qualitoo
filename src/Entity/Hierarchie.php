<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Hierarchie
 */
class Hierarchie
{

	/**
	 * @var Structure
	 */
	private $entiteOne;
	
    /**
     * @var Structure
     */
    private $entiteTwo;
	
    /**
     * @var Structure
     */
    private $entiteThree;
	
    /**
     * @var Structure
     */
    private $entiteFour;
	
    /**
     * @var Structure
     */
    private $entiteFive;
    
    /**
     * @return Structure
     */
	public function getEntiteOne() {
		return $this->entiteOne;
	}
	
	/**
	 * @param Structure $entiteOne
	 * @return Hierarchie
	 */
	public function setEntiteOne($entiteOne) {
		$this->entiteOne = $entiteOne;
		return $this;
	}
	
	/**
     * @return Structure
     */
	public function getEntiteTwo() {
		return $this->entiteTwo;
	}
	
	/**
	 * @param Structure $entiteTwo
	 * @return Hierarchie
	 */
	public function setEntiteTwo($entiteTwo) {
		$this->entiteTwo = $entiteTwo;
		return $this;
	}
	
	/**
	 * @return Structure
	 */
	public function getEntiteThree() {
		return $this->entiteThree;
	}
	
	/**
	 * @param Structure $entite_three
	 * @return Hierarchie
	 */
	public function setEntiteThree($entiteThree) {
		$this->entiteThree = $entiteThree;
		return $this;
	}
	
	/**
	 * @return Structure
	 */
	public function getEntiteFour() {
		return $this->entiteFour;
	}
	
	/**
	 * @param Structure $entiteFour
	 * @return Hierarchie
	 */
	public function setEntiteFour($entiteFour) {
		$this->entiteFour = $entiteFour;
		return $this;
	}
	
	/**
	 * @return Structure
	 */
	public function getEntiteFive() {
		return $this->entiteFive;
	}
	
	/**
	 * @param Structure $entiteFive
	 * @return Hierarchie
	 */
	public function setEntiteFive($entiteFive) {
		$this->entiteFive = $entiteFive;
		return $this;
	}
	
	/**
	 * @param Structure $structure
	 * @return Hierarchie
	 */
	public static function createFromStructure($structure) {
		$hierarchie = new self();
		$data = array('entiteOne', 'entiteTwo', 'entiteThree', 'entiteFour', 'entiteFive');
		while(null != $structure) {
			$hierarchie->{$data[$structure->getLvl()]} = $structure;
			$structure = $structure->getParent();
		}
		return $hierarchie;
	}
}
