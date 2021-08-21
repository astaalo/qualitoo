<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Logs
 * @ORM\Table(name="logs")
 * @ORM\Entity(repositoryClass="Orange\QuickMakingBundle\Repository\LogsRepository")
 */
class Logs
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
     * @ORM\Column(name="nom_entity", type="string")
     */
    private $nomEntity;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="nom_table", type="string")
     */
    private $nomTable;
    
    /**
     * 
     * @var string
     * 
     * @ORM\Column(name="nom_champs", type="string")
     */
    private $nomChamps;
    
    /**
     *
     * @var integer
     * @ORM\Column(name="id_tuple", type="integer")
     */
    private $idTuple;
    
    
    /**
     * 
     * @var string
     * 
     * @ORM\Column(name="valeur_avant", type="string", nullable=true)
     */
    private $valAvant;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="valeur_apres", type="string")
     */
    private $valApres;

    /**
     * @var Operation
     * @ORM\ManyToOne(targetEntity="Operation", cascade={"persist", "merge"})
     * @ORM\JoinColumn(name="operation_id", referencedColumnName="id")
     */
    private $operation;
    
   
    public function __construct(){
    	$this->date = new \DateTime("now");
    }


	public function getId() {
		return $this->id;
	}



    /**
     * Set nomEntity
     *
     * @param string $nomEntity
     * @return Logs
     */
    public function setNomEntity($nomEntity)
    {
        $this->nomEntity = $nomEntity;
    
        return $this;
    }

    /**
     * Get nomEntity
     *
     * @return string 
     */
    public function getNomEntity()
    {
        return $this->nomEntity;
    }

    /**
     * Set nomTable
     *
     * @param string $nomTable
     * @return Logs
     */
    public function setNomTable($nomTable)
    {
        $this->nomTable = $nomTable;
    
        return $this;
    }

    /**
     * Get nomTable
     *
     * @return string 
     */
    public function getNomTable()
    {
        return $this->nomTable;
    }


    /**
     * Set idTuple
     *
     * @param integer $idTuple
     * @return Logs
     */
    public function setIdTuple($idTuple)
    {
        $this->idTuple = $idTuple;
    
        return $this;
    }

    /**
     * Get idTuple
     *
     * @return integer 
     */
    public function getIdTuple()
    {
        return $this->idTuple;
    }

    /**
     * Set valAvant
     *
     * @param string $valAvant
     * @return Logs
     */
    public function setValAvant($valAvant)
    {
        $this->valAvant = $valAvant;
    
        return $this;
    }

    /**
     * Get valAvant
     *
     * @return string 
     */
    public function getValAvant()
    {
        return $this->valAvant;
    }

    /**
     * Set valApres
     *
     * @param string $valApres
     * @return Logs
     */
    public function setValApres($valApres)
    {
        $this->valApres = $valApres;
    
        return $this;
    }

    /**
     * Get valApres
     *
     * @return string 
     */
    public function getValApres()
    {
        return $this->valApres;
    }

    /**
     * Set operation
     *
     * @param \Orange\QuickMakingBundle\Entity\Operation $operation
     * @return Logs
     */
    public function setOperation(\Orange\QuickMakingBundle\Entity\Operation $operation = null)
    {
        $this->operation = $operation;
    
        return $this;
    }

    /**
     * Get operation
     *
     * @return \Orange\QuickMakingBundle\Entity\Operation 
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Set nomChamps
     *
     * @param string $nomChamps
     * @return Logs
     */
    public function setNomChamps($nomChamps)
    {
        $this->nomChamps = $nomChamps;
    
        return $this;
    }

    /**
     * Get nomChamps
     *
     * @return string 
     */
    public function getNomChamps()
    {
        return $this->nomChamps;
    }

    /**
     * 
     * @param unknown $oldValue
     * @param unknown $newValue
     */
    public function loadValue($operation, $nomEntity, $nomTable, $colonne, $idTuple,$idUser, $oldValue, $newValue){
    	$this->setOperation($operation);
    	$this->setNomEntity($nomEntity);
    	$this->setNomTable($nomTable);
    	$this->setNomChamps($colonne);
    	$this->setIdTuple($idTuple);
    	$this->setValAvant($oldValue);
    	$this->setValApres($newValue);
    }
    
}
