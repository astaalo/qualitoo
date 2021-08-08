<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="rapport")
 * @ORM\Entity
 */
class Rapport {
	
	static $types = [
        'risque' => 1,
        'cause' => 2,
        'planAction' => 3,
        'controle' => 4,
        'impact' => 5
    ];
	
	/**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
	private $id;
	
	/**
	 * @var number
	 * @ORM\Column(name="nombre", type="integer", length=11, nullable=false)
	 */
	private $nombre;
	
	/**
	 * @var number
	 * @ORM\Column(name="type", type="integer", length=1, nullable=false)
	 */
	private $type;
	
	/**
	 * @var number
	 * @ORM\Column(name="decription", type="string", length=255, nullable=true)
	 */
	private $description;
	
	/**
	 * @var Chargement
	 * @ORM\ManyToOne(targetEntity="Chargement", inversedBy="rapport")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="chargement_id", referencedColumnName="id")
	 * })
	 */
	private $chargement;
	
	/**
	 * @param number $type
	 * @param number $number
	 * @param string $description
	 * @return Rapport
	 */
	static function newInstance($type, $number, $description) {
		//$self = new self;
		$rapport = new Rapport();
		$rapport->setType($type);
		$rapport->setNombre($number);
		$rapport->setDescription($description);
		return $rapport;
	}

    /**
     * Get id
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
	
	/**
	 * get nombre
	 * @return number
	 */
	public function getNombre() {
		return $this->nombre;
	}
	
	/**
	 * set nombre
	 * @param number $nombre
	 * @return Rapport
	 */
	public function setNombre($nombre) {
		$this->nombre = $nombre;
		return $this;
	}
	
	/**
	 * get type
	 * @return number
	 */
	public function getType() {
		return $this->type;
	}
	
	/**
	 * set type
	 * @param number $type
	 * @return Rapport
	 */
	public function setType($type) {
		$this->type = $type;
		return $this;
	}
	
	/**
	 * get description
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * get description
	 * @return string
	 */
	public function getDescriptionForImpact() {
		$arrData = array();
		$data = json_decode($this->description);
		foreach($data as $domaineId => $nombre) {
			$values = $this->chargement->getCritere()->filter(function($critere) use($domaineId) {
					return $critere->getDomaine()->getId()==$domaineId;
				});
			$libelle = $values->count() ? $values->first()->getDomaine()->getLibelle() : null;
			$arrData[$domaineId] = array(
					'libelle' => $libelle, 'nombre' =>$nombre
				);
		}
		return $arrData;
	}
	
	/**
	 * set description
	 * @param string $description
	 * @return Rapport
	 */
	public function setDescription($description) {
		$this->description = $description;
		return $this;
	}
	
	/**
	 * get chargement
	 * @return Chargement
	 */
	public function getChargement() {
		return $this->chargement;
	}
	
	/**
	 * set chargement
	 * @param Chargement $chargement 
	 * @return Rapport       	
	 */
	public function setChargement($chargement) {
		$this->chargement = $chargement;
		return $this;
	}
	
    
}
