<?php 
namespace App\MainBundle\Twig;

use Symfony\Component\Security\Core\Security;

class SecurityExtension extends \Twig_Extension
{
	/**
	 * @var \App\Entity\Utilisateur
	 */
	private $user;
	
    public function __construct(Security $securityContext)
    {
        $this->user = $securityContext->getToken() ? $securityContext->getToken()->getUser() : null; 
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
        		new \Twig_SimpleFunction('has_rights', array($this, 'hasRights'), array('is_safe' => array('html'))),
        		new \Twig_SimpleFunction('is_proprietaire', array($this, 'isProprietaire'), array('is_safe' => array('html'))),
        		new \Twig_SimpleFunction('show_profil', array($this, 'showProfil'), array('is_safe' => array('html')))
        	);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array();
    }
    
    public function hasRights($role) {
    	if(is_array($role)) {
    		return $this->user ? $this->user->hasRoles($role) : false;
    	} else {
    		return $this->user ? $this->user->hasRole($role) : false;
    	}
    }
    
    public function isProprietaire($document) {
    	return $document->getUtilisateur()->getId()==$this->user->getId() ?  true : false;
    }
    
    public function showProfil($role) {
    	if($role =='ROLE_ADMIN') {
    		return 'Super Administrateur';
    	}  elseif($role == 'ROLE_USER') {
    		return "Tout le mode";
    	}
    }
    
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'orange_main_extension';
    }
}

?>