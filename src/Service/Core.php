<?php
namespace App\Service;

/**
 * @Service("orange_main.core")
 */
class Core {
	
	/**
	 * @param string $name
	 */
	public function getMapping($name, $reporting = false) {
		$class = sprintf('\App\%s\Mapping\%sMapping', $reporting ? 'SyntheseBundle' : 'MainBundle',$name);
		return new $class;
	}
	
	/**
	 * @param string $name
	 */
	public function getReporting($name) {
		$class = sprintf('\App\Reporting\%sReporting', $name);
		return new $class;
	}
	
	/**
	 * @param string $name
	 */
	public function getCriteria($name) {
		$class = sprintf('\App\Criteria\%sCriteria', $name);
		return new $class;
	}
}
