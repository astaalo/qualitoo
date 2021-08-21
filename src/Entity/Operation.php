<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\UtilisateurInterface;

/**
 * Operation
 * @ORM\Table(name="logs_operation")
 * @ORM\Entity
 */
class Operation 
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
     * @var datetime
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UtilisateurInterface")
     * @var UtilisateurInterface
     */
    private $user;
    
    /**
     * 
     * @var string
     * @ORM\Column(name="url", type="string")
     */
    private $url;
    
    public function __construct(){
    	$this->date = new \DateTime("now");
    }

    /**
     * Get id
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     * @param \DateTime $date
     * @return Operation
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set url
     * @param string $url
     * @return Operation
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get url
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set user
     * @param  $user
     * @return Operation
     */
    public function setUser($user = null)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     */
    public function getUser()
    {
        return $this->user;
    }
}
