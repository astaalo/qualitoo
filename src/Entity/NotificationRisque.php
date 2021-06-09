<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotificationRisque
 *
 * @ORM\Table(name="notification_risque")
 * @ORM\Entity
 */
class NotificationRisque
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
     * @var Risque
     *
     * @ORM\ManyToOne(targetEntity="Risque", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="risque_id", referencedColumnName="id")
     * })
     */
    private $risque;

    /**
     * @var Notification
     *
     * @ORM\OneToOne(targetEntity="Notification")
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
     * Set risque
     *
     * @param Risque $risque
     * @return NotificationRisque
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
     * Set notification
     *
     * @param Notification $notification
     * @return NotificationRisque
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
