<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * HistoryEtatRisque
 * @ORM\Table(name="history_etat_risque")
 * @ORM\Entity
 */
class HistoryEtatRisque{
	
	/**
	 * @var integer 
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;
	
	
	/**
	 * @var Risque 
	 * @ORM\ManyToOne(targetEntity="Risque")
	 * @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="risque_id", referencedColumnName="id", onDelete="cascade")
	 * })
	 */
	private $risque;
	
	
	/**
	 * @var integer 
	 * @ORM\Column(name="etat", type="integer")
	 */
	private $etat;
	
	/**
	 * @var String
	 * @ORM\Column(name="comment", type="text", nullable=false)
	 * @Assert\NotNull(message="Le motif est obligatoire")
	 */
	private $comment; 
	
	/**
	 * @var Utilisateur
	 * @ORM\ManyToOne(targetEntity="Utilisateur")
	 * @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id", onDelete="SET NULL")
	 * })
	 */
	private $utilisateur;
	
	/**
	 * @var \DateTime
	 * @ORM\Column(name="date", type="datetime", nullable=true)
	 *
	 */
	private $date;

	
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->date=new \DateTime("NOW");
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
     * Set etat
     *
     * @param integer $etat
     * @return HistoryEtatRisque
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;
    
        return $this;
    }

    /**
     * Get etat
     *
     * @return integer 
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return HistoryEtatRisque
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    
        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set risque
     *
     * @param Risque $risque
     * @return HistoryEtatRisque
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
     * Set date
     *
     * @param \DateTime $date
     * @return HistoryEtatRisque
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set utilisateur
     *
     * @param Utilisateur $utilisateur
     * @return HistoryEtatRisque
     */
    public function setUtilisateur(Utilisateur $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;
    
        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return Utilisateur
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }
}
