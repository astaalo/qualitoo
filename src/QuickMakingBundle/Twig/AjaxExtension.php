<?php

namespace App\QuickMakingBundle\Twig;

class AjaxExtension extends \Twig_Extension
{
	private $environment;
	
    public function __construct(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }
	
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('ajax_list_action', array($this, 'doActionInList'))
        );
    }

    public function doActionInList($actionUrl, $successCallback, $errorCallback) {
    	return $this->environment->render('extra/ajax.html.twig', array('actionUrl' => $actionUrl, 'successCallback' => $successCallback, 'errorCallback' => $errorCallback));
    }
    
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'Ajax';
    }
}