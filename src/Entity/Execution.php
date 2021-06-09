<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Absctract\NotificationInterface;
use App\Utils\NotificationUtil;

/**
 * Execution
 *
 * @ORM\Table(name="execution")
 * @ORM\Entity
 */
class Execution implements NotificationInterface
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
     * @var Utilisateur
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="executeur", referencedColumnName="id")
     * })
     */
    private $executeur;

    /**
     * @var \DateTime
     * @ORM\Column(name="date_execution", type="datetime", nullable=false)
     */
    private $dateExecution;

    /**
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="validateur", referencedColumnName="id")
     * })
     */
    private $validateur;

    /**
     * @var \DateTime
     * @ORM\Column(name="date_validation", type="datetime", nullable=true)
     */
    private $dateValidation;
	
	/**
	 * @var \Symfony\Component\HttpFoundation\File\UploadedFile
	 * @Assert\File(maxSize="6000000", mimeTypesMessage="Only CSV files are allowed.")
	 */
	public $file;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	public $path;

    /**
     * @var boolean
     * @ORM\Column(name="valide", type="boolean", nullable=true)
     */
    private $valide;

    /**
     * @var string
     * @ORM\Column(name="commentaire", type="text", nullable=false)
     * @Assert\NotNull(message="Veuillez saisir le commentaire SVP")
     */
    private $commentaire;

    /**
     * @var Controle
     * @ORM\ManyToOne(targetEntity="App\Entity\Controle")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="controle_id", referencedColumnName="id")
     * })
     */
    private $controle;

    public function __construct() {
    	$this->dateExecution = new \DateTime('NOW');
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
     * Set dateExecution
     *
     * @param \DateTime $dateExecution
     * @return Execution
     */
    public function setDateExecution($dateExecution)
    {
        $this->dateExecution = $dateExecution;

        return $this;
    }

    /**
     * Get dateExecution
     *
     * @return \DateTime 
     */
    public function getDateExecution()
    {
        return $this->dateExecution;
    }

    /**
     * Set executeur
     * @param Utilisateur $executeur
     * @return Execution
     */
    public function setExecuteur(Utilisateur $executeur = null)
    {
        $this->executeur = $executeur;
        return $this;
    }

    /**
     * Get executeur
     * @return Utilisateur
     */
    public function getExecuteur()
    {
        return $this->executeur;
    }

    /**
     * Set validateur
     * @param Utilisateur $validateur
     * @return Execution
     */
    public function setValidateur(Utilisateur $validateur = null)
    {
        $this->validateur = $validateur;
        return $this;
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
     * Set dateValidation
     * @param \DateTime $dateValidation
     * @return Execution
     */
    public function setDateValidation($dateValidation)
    {
        $this->dateValidation = $dateValidation;
        return $this;
    }

    /**
     * Get dateValidation
     * @return \DateTime 
     */
    public function getDateValidation()
    {
        return $this->dateValidation;
    }

    /**
     * Set valide
     * @param boolean $valide
     * @return Execution
     */
    public function setValide($valide)
    {
        $this->valide = $valide;
        return $this;
    }

    /**
     * Get valide
     * @return boolean 
     */
    public function isValide()
    {
        return $this->valide;
    }
    
    /**
     * set controle
     * @param Controle $controle
     * @return Execution
     */
    public function setControle($controle) {
    	$this->controle = $controle;
    	return $this;
    }
    
    /**
     * get controle
     * @return Controle
     */
    public function getControle() {
    	return $this->controle;
    }
    
    /**
     * get commentaire
     * @return string
     */
    public function getCommentaire() {
    	$this->commentaire;
    }
    
    /**
     * set commentaire
     * @param string $commentaire
     * @return Execution
     */
    public function setCommentaire($commentaire) {
    	$this->commentaire = $commentaire;
    	return $this;
    }
	
	public function getAbsolutePath() {
		return null === $this->path ? null : $this->getUploadRootDir () . '/' . $this->path;
	}
	public function getWebPath() {
		return null === $this->path ? null : $this->getUploadDir() . '/' . $this->path;
	}
	protected function getUploadRootDir() {
		// le chemin absolu du r�pertoire o� les documents upload�s doivent �tre sauvegard�s
		return __DIR__ . '/../../../../web/' . $this->getUploadDir();
	}
	protected function getUploadDir() {
		return 'uploads/preuves';
	}
	
	public function upload() {
		if (null === $this->file) {
			return;
		}
		$this->file->move($this->getUploadRootDir(), $this->file->getClientOriginalName());
		$this->path = $this->file->getClientOriginalName();
		$this->file = null;
	}
	
	/* (non-PHPdoc)
	 * @see Absctract\NotificationInterface::generateNotification()
	 */
	public function generateNotification(Utilisateur $user, TypeNotification $type, $isNew) {
		// TODO: Auto-generated method stub
		
		$label = 'Execution de contrôle';
		$desc  = sprintf('Bonjour, <br> <b> %s </b> a executé le contrôle : <span style="color: #f60;">%s</span>, du risque <b>"%s"</b>"', $user, $this->getControle(), $this->getControle()->getRisque());
		
		$notification = NotificationUtil::create($label, $desc, $this, $type, $user);
		$notification->addReceiver($this->getControle()->getSuperviseur());
		$notification->addReceiver($this->getControle()->getPorteur());
		
		$riskManagers = $this->getControle()->getRisque()->getActivite()->getProcessus()->getStructure()->getSociete()->getRiskManager();
		foreach ($riskManagers as $riskManager) {
			$notification->addReceiver($riskManager);
		}

		return $notification;
	}

	/* (non-PHPdoc)
	 * @see Absctract\NotificationInterface::generateWorkflow()
	 */
	public function generateWorkflow(Notification $notification, TypeNotification $type, $isNew) {
		// TODO: Auto-generated method stub
		$mail = array();
		
		$subject = 'Execution de contrôle';
		$action = 'Execution de contrôle';
		$content = sprintf('Bonjour, <br> <b> %s </b> a executé le contrôle : <span style="color: #f60;">%s</span>, au risque <b>"%s"</b>', $notification->getUser(), $this->getControle()->getLibelle(), $this->getControle()->getRisque()); 
		
		$mail['subject'] = $subject;
		$mail['body']['action'] = $action;
		$mail['body']['content'] = $content;
		$mail['body']['link'] = '#';
		$mail['recipients'] = array();

		$mail['recipients'][$notification->getUser()->getEmail()] = $notification->getUser()->__toString();
		
		foreach ($notification->getReceivers() as $receiver) {
			$mail['recipients'][$receiver->getEmail()] = $receiver->__toString();
		}
		
		return $mail;
	}


    /**
     * Set path
     *
     * @param string $path
     * @return Execution
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get valide
     *
     * @return boolean 
     */
    public function getValide()
    {
        return $this->valide;
    }
}
