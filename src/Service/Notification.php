<?php 
namespace App\Service;

//use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Notification
{
	/**
	 * @var \App\Entity\Utilisateur
	 */
	private $user;

	private $container;

	/**
	 * 
	 * @var EntityManager
	 */
	private $em;
	
    public function __construct(ContainerInterface $container)
    {
    	$this->container = $container;
    	$securityContext= $container->get('security.token_storage');
        $this->user = $securityContext->getToken() ? $securityContext->getToken()->getUser() : null; 
        $this->em = $container->get('doctrine')->getManager();
    }
	
    /**
     * Returns the number of notifications for the current user
     *
     * @return integer The number of notifications for the current user
     */
    public function getNotificationNumber() {
    	return $this->em->getRepository(\App\Entity\Notification::class)->getCount($this->user);
    }
	
    /**
     * Returns the list of notifications
     *
     * @return Collection The notifications for the current user
     */
    public function getUnreadNotifications() {
    	return $this->em->getRepository(\App\Entity\Notification::class)->getUnreadNotifications($this->user);
    }
}

?>