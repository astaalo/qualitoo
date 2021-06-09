<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Workflow
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Workflow
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
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
     * @var \Utilisateur
     *
     * @ORM\ManyToMany(targetEntity="Utilisateur")
     */
    private $receivers;
	
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
     * Set subject
     *
     * @param string $subject
     * @return Workflow
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }
	
    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Workflow
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Workflow
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
     * Constructor
     */
    public function __construct()
    {
        $this->receivers = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Add receivers
     *
     * @param Utilisateur $receivers
     * @return Workflow
     */
    public function addReceiver(Utilisateur $receivers)
    {
        $this->receivers[] = $receivers;

        return $this;
    }

    /**
     * Remove receivers
     *
     * @param Utilisateur $receivers
     */
    public function removeReceiver(Utilisateur $receivers)
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
     * Create workflow from array
     *
     * @return Workflow 
     */
    public static function createFromArray($data)
    {
//     	$workflow = new Workflow();
//     	$workflow->setSubject($data['subject']);
//     	$workflow->setContent($data['body']['content']);
//     	$workflow->setDate(new \DateTime());
    	
//     	foreach ($data['recipients'] as $receiver) {
//     		$workflow->addReceiver($receiver);
//     	}
    	
//         return $workflow;
    }
}
