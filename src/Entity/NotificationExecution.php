<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotificationExecution
 *
 * @ORM\Table(name="notification_execution")
 * @ORM\Entity
 */
class NotificationExecution
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
     * @var \Execution
     *
     * @ORM\ManyToOne(targetEntity="Execution", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="execution_id", referencedColumnName="id")
     * })
     */
    private $execution;
	
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
     * Set execution
     *
     * @param Execution $execution
     * @return NotificationExecution
     */
    public function setExecution(Execution $execution = null)
    {
        $this->execution = $execution;

        return $this;
    }
	
    /**
     * Get execution
     *
     * @return Execution
     */
    public function getExecution()
    {
        return $this->execution;
    }
	
    /**
     * Set notification
     *
     * @param Notification $notification
     * @return NotificationExecution
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
