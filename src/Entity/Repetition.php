<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Domaine
 * @ORM\Table(name="repetition")
 * @ORM\Entity(repositoryClass="App\Repository\RepetitionRepository")
 */
class Repetition 
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
     * @var \DateTime
     *
     * @ORM\Column(name="date_repetition", type="date", nullable=true)
     */
    private $dateRepetition;
    
   /**
     * @var Relance
     * @ORM\ManyToOne(targetEntity="Relance", inversedBy="repetitions")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="relance_id", referencedColumnName="id")
     * })
     */
    private $relance;
    
    /**
     * @var Risque
     * @ORM\ManyToOne(targetEntity="Risque", inversedBy="repetitionRelances")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="risque_id", referencedColumnName="id")
     * })
     */
    private $risque;
    
    public function __construct(){
    	$this->dateRepetition=new \DateTime("NOW");;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set relance
     *
     * @param Relance $relance
     * @return Repetition
     */
    public function setRelance(Relance $relance = null)
    {
        $this->relance = $relance;
    
        return $this;
    }

    /**
     * Get relance
     *
     * @return Relance
     */
    public function getRelance()
    {
        return $this->relance;
    }

    /**
     * Set risque
     *
     * @param Risque $risque
     * @return Repetition
     */
    public function setRisque(Risque $risque = null)
    {
        $this->risque = $risque;
    
        return $this;
    }

    /**
     * Get risque
     *
     * @return Risque
     */
    public function getRisque()
    {
        return $this->risque;
    }

    /**
     * Set dateRepetition
     *
     * @param \DateTime $dateRepetition
     * @return Repetition
     */
    public function setDateRepetition($dateRepetition)
    {
        $this->dateRepetition = $dateRepetition;
    
        return $this;
    }

    /**
     * Get dateRepetition
     *
     * @return \DateTime 
     */
    public function getDateRepetition()
    {
        return $this->dateRepetition;
    }
}
