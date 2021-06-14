<?php
namespace App\MainBundle\Twig;


use Twig\TwigFilter;

class StatutExtension extends \Twig_Extension
{

	/**
	 * @var \Twig_Environment
	 */
	private $twig;
	
	/**
	 * @var array
	 */
	private $ids;
	
	public function __construct(\Twig_Environment $twig, $ids) {
		$this->ids = $ids;
		$this->twig = $twig;
	}
    
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
                new TwigFilter('show_status', [$this, 'getStatus'], array('is_safe' => array('html')))
        	);
    }
    
    /**
     * get status's entity
     * @param Mixed $entity
     * @return string
     */
    public function getStatus($entity, $options = array()) {
    	if(!$entity) {
    		return;
    	}
    	$reflect = new \ReflectionClass($entity);
    	$template = $this->twig->loadTemplate('extra/status.html.twig');
    	return $template->renderBlock('status_'.strtolower($reflect->getShortName()), array('entity' => $entity, 'global_ids' => $this->ids, 'options' => $options));
    }
	
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'statut_extension';
    }
}
