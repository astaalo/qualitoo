<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Activite
 *
 * @ORM\Table(name="historique_evaluation")
 * @ORM\Entity(repositoryClass="App\Repository\HistoriqueEvaluationRepository")
 */
class HistoriqueEvaluation
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
     * @ORM\ManyToOne(targetEntity="Risque", cascade={"persist", "merge"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="risque_id", referencedColumnName="id")
     * })
     */
    private $risque;
	
    /**
     * @var Evaluation
     * @ORM\ManyToOne(targetEntity="Evaluation", cascade={"persist", "merge"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="derniere_evaluation_id", referencedColumnName="id")
     * })
     */
    private $lastEvaluation;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_evaluation", type="datetime", nullable=true)
     */
    private $dateEvaluation;
    
	

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
     * Set dateEvaluation
     *
     * @param \DateTime $dateEvaluation
     * @return HistoriqueEvaluation
     */
    public function setDateEvaluation($dateEvaluation)
    {
        $this->dateEvaluation = $dateEvaluation;
    
        return $this;
    }

    /**
     * Get dateEvaluation
     *
     * @return \DateTime 
     */
    public function getDateEvaluation()
    {
        return $this->dateEvaluation;
    }

    /**
     * Set risque
     *
     * @param Risque $risque
     * @return HistoriqueEvaluation
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
     * Set lastEvaluation
     *
     * @param Evaluation $lastEvaluation
     * @return HistoriqueEvaluation
     */
    public function setLastEvaluation(Evaluation $lastEvaluation = null)
    {
        $this->lastEvaluation = $lastEvaluation;
    
        return $this;
    }

    /**
     * Get lastEvaluation
     *
     * @return Evaluation
     */
    public function getLastEvaluation()
    {
        return $this->lastEvaluation;
    }
}
