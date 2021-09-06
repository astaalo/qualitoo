<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotificationRepository::class)
 */
class Notification
{
    const TYPE_CONTROLE = 'notification_controle';
    const TYPE_EXECUTION_CONTROLE = 'notification_execution_controle';
    const TYPE_RISQUE = 'notification_risque';
    const TYPE_PA = 'notification_pa';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_read", type="boolean")
     */
    private $read;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModification", type="datetime")
     */
    private $dateModification;

    

   

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Utilisateur", inversedBy="notifications")
     * @ORM\JoinTable(name="notification_receivers",
     *   joinColumns={
     *     @ORM\JoinColumn(name="notification_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *   }
     * )
     */
    private $receivers;

   

   

    


    public function __construct() {
        $this->dateCreation = new \DateTime();
        $this->dateModification = new \DateTime();
        $this->read = false;
        $this->receivers = new ArrayCollection();
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
     * Set libelle
     *
     * @param string $libelle
     * @return Notification
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Notification
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set read
     *
     * @param boolean $read
     * @return Notification
     */
    public function setRead($read)
    {
        $this->read = $read;

        return $this;
    }

    /**
     * Is read
     *
     * @return boolean
     */
    public function isRead()
    {
        return $this->read;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Notification
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set dateModification
     *
     * @param \DateTime $dateModification
     * @return Notification
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    /**
     * Get dateModification
     *
     * @return \DateTime
     */
    public function getDateModification()
    {
        return $this->dateModification;
    }

    /**
     * Set type
     *
     * @param TypeNotification $type
     * @return Notification
     */
    public function setType(TypeNotification $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return TypeNotification
     */
    public function getType()
    {
        return $this->type;
    }

  
    /**
     * Set notificationControle
     *
     * @param NotificationControle $notificationControle
     * @return Notification
     */
    public function setNotificationControle(NotificationControle $notificationControle = null)
    {
        $notificationControle->setNotification($this);
        $this->notificationControle = $notificationControle;

        return $this;
    }

    /**
     * Get notificationControle
     *
     * @return NotificationControle
     */
    public function getNotificationControle()
    {
        return $this->notificationControle;
    }

    /**
     * Set notificationRisque
     *
     * @param NotificationRisque $notificationRisque
     * @return Notification
     */
    public function setNotificationRisque(NotificationRisque $notificationRisque = null)
    {
        $notificationRisque->setNotification($this);
        $this->notificationRisque = $notificationRisque;

        return $this;
    }

    /**
     * Get notificationRisque
     *
     * @return NotificationRisque
     */
    public function getNotificationRisque()
    {
        return $this->notificationRisque;
    }  
}
