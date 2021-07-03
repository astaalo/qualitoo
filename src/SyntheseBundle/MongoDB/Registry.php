<?php

/*
 * This file is part of the Doctrine Bundle
 *
 * The code was originally distributed inside the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 * (c) Doctrine Project, Benjamin Eberlei <kontakt@beberlei.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\SyntheseBundle\MongoDB;


use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * References all Doctrine connections and entity managers in a given Container.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Registry extends ManagerRegistry
{
	
	public function __construct($name, array $connections, array $managers, $defaultConnection, $defaultManager, $proxyInterfaceName, ContainerInterface $container = null)
	{
		try {
			$token = $container->get('security.context')->getToken();
			$user = $token ? $token->getUser() : null;
		} catch(\Exception $e) {
			$user = null;
		}
		foreach($managers as $manager) {
			$container->get($manager)->setParameters(
					$container->hasParameter('ids') ? $container->getParameter('ids') : array(),
					$container->hasParameter('states') ? $container->getParameter('states') : array(),
					$user
				);
		}
		parent::__construct($name, $connections, $managers, $defaultConnection, $defaultManager, $proxyInterfaceName, $container);
	}
	
	/**
	 * Resolves a registered namespace alias to the full namespace.
	 *
	 * @param string $alias
	 * @return string
	 * @throws MongoDBException
	 */
	public function getAliasNamespace($alias)
	{
		try {
			$token = $container->get('security.context')->getToken();
			$user = $token ? $token->getUser() : null;
		} catch(\Exception $e) {
			$user = null;
		}
		foreach (array_keys($this->getManagers()) as $name) {
			try {
				$em = $this->getManager($name);
				$defaultManager->setParameters(
						$container->hasParameter('ids') ? $container->getParameter('ids') : array(),
						$container->hasParameter('states') ? $container->getParameter('states') : array(),
						$user
					);
				return $em->getConfiguration()->getDocumentNamespace($alias);
			} catch (MongoDBException $e) {
			}
		}
		
		throw MongoDBException::unknownDocumentNamespace($alias);
	}
}
