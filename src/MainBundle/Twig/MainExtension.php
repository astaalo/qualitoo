<?php
namespace App\MainBundle\Twig;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\TwigFilter;

class MainExtension extends \Twig_Extension {
	
	/**
	 * @var \Symfony\Component\Routing\Router
	 */
	private $router;
	
	public function __construct(ContainerInterface $container) {
		$this->router = $container->get('router');
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return array();
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getFilters()
	{
        return array(
                new TwigFilter('aggregate_link', [$this, 'showAggregateLink'], array('is_safe' => array('html')))
        	);
	}
    
    /**
     * get status's entity
     * @param integer $aggregate
     * @param string $libelle
     * @param array $current_aggregate
     * @param integer $carto
     * @return string
     */
    public function showAggregateLink($aggregate, $libelle, $current_aggregate, $carto) {
    	$class = ($current_aggregate[$carto]==$aggregate) ? 'special' : null;
    	return sprintf('<li><a class="uibutton %s" href="%s">%s</a></li>', 
    			$class, $this->router->generate('aggregat_restitution', array('carto' => $carto, 'group' => $aggregate)), ucfirst($libelle)
    		);
    }
	
	/**
	 * Returns the name of the extension.
	 *
	 * @return string The extension name
	 */
	public function getName() {
		return 'main';
	}
}
