<?php
namespace Orange\MainBundle\Services;

/**
 * @Service("orange_main.core")
 */
class Core {
	
	/**
	 * @param string $name
	 */
	public function getMapping($name, $reporting = false) {
		$class = sprintf('\Orange\%s\Mapping\%sMapping', $reporting ? 'SyntheseBundle' : 'MainBundle',$name);
		return new $class;
	}
	
	/**
	 * @param string $name
	 */
	public function getReporting($name) {
		$class = sprintf('\Orange\MainBundle\Reporting\%sReporting', $name);
		return new $class;
	}
	
	/**
	 * @param string $name
	 */
	public function getCriteria($name) {
		$class = sprintf('\Orange\MainBundle\Criteria\%sCriteria', $name);
		return new $class;
	}
}