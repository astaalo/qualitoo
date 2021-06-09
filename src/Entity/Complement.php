<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * Complement
 *
 * @ORM\Table(name="complement")
 * @ORM\Entity
 */
class Complement
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
     * @var Risque
     * @ORM\OneToOne(targetEntity="Risque")
     * @ORM\JoinColumn(name="risque_id", referencedColumnName="id")
     */
    private $risque;
    
    
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return Risque
	 */
	public function getRisque() {
		return $this->risque;
	}
	
	/**
	 * @param Risque $risque
	 * @return Complement
	 */
	public function setRisque($risque) {
		$this->risque = $risque;
		return $this;
	}
	
	
}
