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
     * @var TypeNotification
     *
     * @ORM\ManyToOne(targetEntity="TypeNotification")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     * })
     */
    private $type;

    /**
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

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

    /**
     * @var NotificationControle
     * @ORM\OneToOne(targetEntity="NotificationControle", mappedBy="notification", cascade={"persist", "merge", "remove"})
     */
    private $notificationControle;

    /**
     * @var NotificationRisque
     * @ORM\OneToOne(targetEntity="NotificationRisque", mappedBy="notification", cascade={"persist", "merge", "remove"})
     */
    private $notificationRisque;

    /**
     * @var NotificationExecution
     * @ORM\OneToOne(targetEntity="NotificationExecution", mappedBy="notification", cascade={"persist", "merge", "remove"})
     */
    private $notificationExecution;

    /**
     * @var NotificationPlanAction
     * @ORM\OneToOne(targetEntity="NotificationPlanAction", mappedBy="notification", cascade={"persist", "merge", "remove"})
     */
    private $notificationPa;


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
     * @param \App\Entity\TypeNotification $type
     * @return Notification
     */
    public function setType(\App\Entity\TypeNotification $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \App\Entity\TypeNotification
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set user
     *
     * @param \App\Entity\Utilisateur $user
     * @return Notification
     */
    public function setUser(\App\Entity\Utilisateur $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \App\Entity\Utilisateur
     */
    public function getUser()
    {
        return $this->user;
    }

    public function __toString() {
        return $this->libelle;
    }

    /**
     * Get read
     *
     * @return boolean
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * Set notificationControle
     *
     * @param \App\Entity\NotificationControle $notificationControle
     * @return Notification
     */
    public function setNotificationControle(\App\Entity\NotificationControle $notificationControle = null)
    {
        $notificationControle->setNotification($this);
        $this->notificationControle = $notificationControle;

        return $this;
    }

    /**
     * Get notificationControle
     *
     * @return \App\Entity\NotificationControle
     */
    public function getNotificationControle()
    {
        return $this->notificationControle;
    }

    /**
     * Set notificationRisque
     *
     * @param \App\Entity\NotificationRisque $notificationRisque
     * @return Notification
     */
    public function setNotificationRisque(\App\Entity\NotificationRisque $notificationRisque = null)
    {
        $notificationRisque->setNotification($this);
        $this->notificationRisque = $notificationRisque;

        return $this;
    }

    /**
     * Get notificationRisque
     *
     * @return \App\Entity\NotificationRisque
     */
    public function getNotificationRisque()
    {
        return $this->notificationRisque;
    }


    /**
     * Add receivers
     *
     * @param \App\Entity\Utilisateur $receivers
     * @return Notification
     */
    public function addReceiver(\App\Entity\Utilisateur $receivers)
    {
        $this->receivers->set($receivers->getId(), $receivers);
        return $this;
    }

    /**
     * Remove receivers
     *
     * @param \App\Entity\Utilisateur $receivers
     */
    public function removeReceiver(\App\Entity\Utilisateur $receivers)
    {
        $this->receivers->removeElement($receivers);
    }

    /**
     * Get receivers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReceivers()
    {
        return $this->receivers;
    }

    /**
     * Set notificationExecution
     *
     * @param \App\Entity\NotificationExecution $notificationExecution
     * @return Notification
     */
    public function setNotificationExecution(\App\Entity\NotificationExecution $notificationExecution = null)
    {
        $notificationExecution->setNotification($this);
        $this->notificationExecution = $notificationExecution;

        return $this;
    }

    /**
     * Get notificationExecution
     *
     * @return \App\Entity\NotificationExecution
     */
    public function getNotificationExecution()
    {
        return $this->notificationExecution;
    }

    /**
     * Set notificationPa
     *
     * @param \App\Entity\NotificationPlanAction $notificationPa
     * @return Notification
     */
    public function setNotificationPlanAction(\App\Entity\NotificationPlanAction $notificationPa = null)
    {
        $notificationPa->setNotification($this);
        $this->notificationPa = $notificationPa;

        return $this;
    }

    /**
     * Get notificationPa
     *
     * @return \App\Entity\NotificationPlanAction
     */
    public function getNotificationPlanAction()
    {
        return $this->notificationPa;
    }

    /**
     * @return array
     */
    public function getNotificationLink() {
        $linkParams = null;
        switch ($this->getType()->getCode()) {
            case self::TYPE_CONTROLE :
                $linkParams = array('name' => 'details_controle', 'params' => array('id' => $this->getNotificationControle()->getControle()->getId()));
                break;
            case self::TYPE_RISQUE:
                $risque = $this->getNotificationRisque()->getRisque();
                if($risque->isValidated()) {
                    $linkParams = array('name' => 'details_risque', 'params' => array('id' => $this->getNotificationRisque()->getRisque()->getId()));
                } else {
                    $linkParams = array('name' => 'risques_a_valider', 'params' => array());
                }
                break;
            case self::TYPE_PA:
                $linkParams = array('name' => 'details_planaction', 'params' => array('id' => $this->getNotificationPlanAction()->getPlanAction()->getId()));
                break;
            case self::TYPE_EXECUTION_CONTROLE:
                $controle = $this->getNotificationExecution()->getExecution()->getControle();
                $linkParams = array('name' => 'details_controle', 'params' => array('id' => $controle->getId()));
                break;
        }

        return $linkParams;
    }

    /**
     * Set notificationPa
     *
     * @param \App\Entity\NotificationPlanAction $notificationPa
     * @return Notification
     */
    public function setNotificationPa(\App\Entity\NotificationPlanAction $notificationPa = null)
    {
        $this->notificationPa = $notificationPa;

        return $this;
    }

    /**
     * Get notificationPa
     *
     * @return \App\Entity\NotificationPlanAction
     */
    public function getNotificationPa()
    {
        return $this->notificationPa;
    }
}
