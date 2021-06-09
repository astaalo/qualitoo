<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotificationControle
 *
 * @ORM\Table(name="notification_controle")
 * @ORM\Entity
 */
class NotificationControle
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Controle
     *
     * @ORM\ManyToOne(targetEntity="Controle", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="controle_id", referencedColumnName="id")
     * })
     */
    private $controle;

    /**
     * @var Notification
     *
     * @ORM\ManyToOne(targetEntity="Notification")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="notification_id", referencedColumnName="id")
     * })
     */
    private $notification;
    
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
     * Set controle
     *
     * @param Controle $controle
     * @return NotificationControle
     */
    public function setControle(Controle $controle = null)
    {
        $this->controle = $controle;

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
     * Set notification
     *
     * @param Notification $notification
     * @return NotificationControle
     */
    public function setNotification(Notification $notification = null)
    {
        $this->notification = $notification;

        return $this;
    }

    /**
     * Get notification
     *
     * @return Notification
     */
    public function getNotification()
    {
        return $this->notification;
    }
}
