<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotificationPlanAction
 *
 * @ORM\Table(name="notification_pa")
 * @ORM\Entity
 */
class NotificationPlanAction
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
     * @var PlanAction
     *
     * @ORM\ManyToOne(targetEntity="PlanAction", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pa_id", referencedColumnName="id")
     * })
     */
    private $pa;

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
     * Set pa
     *
     * @param PlanAction $pa
     * @return NotificationPlanAction
     */
    public function setPlanAction(PlanAction $pa = null)
    {
        $this->pa = $pa;

        return $this;
    }

    /**
     * Get pa
     *
     * @return PlanAction
     */
    public function getPlanAction()
    {
        return $this->pa;
    }

    /**
     * Set notification
     *
     * @param Notification $notification
     * @return NotificationPlanAction
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

    /**
     * Set pa
     *
     * @param PlanAction $pa
     * @return NotificationPlanAction
     */
    public function setPa(PlanAction $pa = null)
    {
        $this->pa = $pa;
    
        return $this;
    }

    /**
     * Get pa
     *
     * @return PlanAction
     */
    public function getPa()
    {
        return $this->pa;
    }
}
