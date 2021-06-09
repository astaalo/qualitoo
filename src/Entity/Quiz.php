<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Quiz
 *
 * @ORM\Table(name="quiz")
 * @ORM\Entity(repositoryClass="App\Repository\QuizRepository")
 */
class Quiz
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
     * @ORM\Column(name="date_evaluation", type="datetime", nullable=false)
     */
    private $dateEvaluation;
	
    /**
     * @var \Maturite
     *
     * @ORM\ManyToOne(targetEntity="Maturite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="maturite_id", referencedColumnName="id")
     * })
     */
    private $maturite;
	
    /**
     * @var \Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="testeur", referencedColumnName="id")
     * })
	 * @Assert\NotNull(message="Le champ controle est obligatoire")
     */
    private $testeur;

    /**
     * @var \DateTime
     * @ORM\Column(name="date_validation", type="datetime", nullable=true)
     */
    private $dateValidation;
	
    /**
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="validateur", referencedColumnName="id")
     * })
	 * @Assert\NotNull(message="Le champ controle est obligatoire")
     */
    private $validateur;
	
    /**
     * @var Controle
     * @ORM\ManyToOne(targetEntity="Controle")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="controle_id", referencedColumnName="id")
     * })
	 * @Assert\NotNull(message="Le champ évaluateur est obligatoire")
     */
    private $controle;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Reponse", mappedBy="quiz", cascade={"persist", "merge", "remove"})
     */
    protected $reponse;
	
    public function __construct() {
    	$this->dateEvaluation = new \DateTime();
    	$this->reponse = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set dateEvaluation
     *
     * @param \DateTime $dateEvaluation
     * @return Quiz
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
     * Set dateEvaluation
     *
     * @param \DateTime $dateEvaluation
     * @return Quiz
     */
    public function setDateValidation($dateValidation)
    {
    	$this->dateValidation = $dateValidation;
    
    	return $this;
    }
    
    /**
     * Get dateValidation
     *
     * @return \DateTime
     */
    public function getDateValidation()
    {
    	return $this->dateValidation;
    }
    
    /**
     * Set maturite
     *
     * @param Maturite $maturite
     * @return Quiz
     */
    public function setMaturite($maturite)
    {
        $this->maturite = $maturite;

        return $this;
    }

    /**
     * Get maturite
     * @return Maturite 
     */
    public function getMaturite()
    {
        return $this->maturite;
    }

    /**
     * Set testeur
     * @param Utilisateur $testeur
     * @return Quiz
     */
    public function setTesteur(Utilisateur $testeur = null)
    {
        $this->testeur = $testeur;

        return $this;
    }

    /**
     * Set controle
     * @param Controle $controle
     * @return Quiz
     */
    public function setControle(Controle $controle = null)
    {
        $this->controle = $controle;
        return $this;
    }

    /**
     * Get testeur
     * @return Utilisateur
     */
    public function getTesteur()
    {
        return $this->testeur;
    }

    /**
     * Get validateur
     * @return Utilisateur
     */
    public function getValidateur()
    {
        return $this->validateur;
    }

    /**
     * set validateur
     * @param Utilisateur $validateur
     * @return Quiz
     */
    public function setValidateur($validateur)
    {
        $this->validateur = $validateur;
        return $this;
    }

    /**
     * Get controle
     *
     * @return Controle
     */
    public function getControle()
    {
        return $this->controle;
    }
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
	public function getReponse() {
		return $this->reponse;
	}
    
    /**
     * @return Quiz
     */
	public function setReponse($reponse) {
		$this->reponse = $reponse;
		return $this;
	}
    
    public function loadQuestions($questions) {
    	foreach($questions as $question) {
    		if($question->getEtat()) {
    			$reponse = new Reponse();
    			$reponse->setQuiz($this);
    			$reponse->setQuestion($question);
    			$reponse->setNumero($question->getPosition());
    		    $this->reponse->add($reponse);
    		}
    	}
    	return $this;
    }
    
    public function getRapport() {
    	$validCount = $cotation = 0;
    	foreach($this->reponse as $reponse) {
    		$cotation += $reponse->getQuestion()->getCotation();
    		if($reponse->isValide()) {
    			$validCount += $reponse->getQuestion()->getCotation();
    		}
    	}
    	$maturite = ($cotation!=0) ? intval(round($validCount * 4 / $cotation)) :0;
    	// if maturite == 0, return 1
    	return $maturite == 0 ? 1 : $maturite;
    }
	

    /**
     * Add reponse
     *
     * @param Reponse $reponse
     * @return Quiz
     */
    public function addReponse(Reponse $reponse)
    {
        $this->reponse[] = $reponse;
    
        return $this;
    }

    /**
     * Remove reponse
     *
     * @param Reponse $reponse
     */
    public function removeReponse(Reponse $reponse)
    {
        $this->reponse->removeElement($reponse);
    }
}
