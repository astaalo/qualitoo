<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ConfigurationController extends BaseController
{
	/**
	 * @Route("/configuration", name="configuration")
	 * @Template()
	 */
	public function indexAction() {
		return array();
	}
	
	
}
