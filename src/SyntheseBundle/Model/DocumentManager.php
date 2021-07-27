<?php
namespace App\SyntheseBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectRepository;
use MongoDB\Client;
use MongoDB\Client as Connection;
//use Doctrine\MongoDB\Connection;
use Doctrine\Common\EventManager;
use Doctrine\ODM\MongoDB\Configuration;

class DocumentManager extends \Doctrine\ODM\MongoDB\DocumentManager
{
    /**
     * The used Parameters.
     *
     * @var ArrayCollection
     */
    private $_parameters;

    /*public static function create(Connection $conn = null, Configuration $config = null, EventManager $eventManager = null)
    {
        $dm = parent::create($conn, $config, $eventManager);
        return new DocumentManager($dm->getConnection(), $dm->getConfiguration(), $dm->getEventManager());
    }*/

    public static function create(?Client $client = null, ?Configuration $config = null, ?EventManager $eventManager = null): \Doctrine\ODM\MongoDB\DocumentManager
    {
        return new static($client, $config, $eventManager);
    }
    
    /**
     * Gets the repository for a document class.
     *
     * @param string $documentName  The name of the Document.
     * @return ObjectRepository  The repository.
     */
    public function getRepository($documentName)
    {
    	$repository = parent::getRepository($documentName);
    	if(method_exists($repository, 'setParameters')) {
    		$repository->setParameters($this->_parameters);
    	}
    	return $repository;
    }

    public function setParameters($ids, $states, $user) {
    	$this->_parameters = array();
    	$this->_parameters['ids'] = $ids;
    	$this->_parameters['states'] = $states;
    	$this->_parameters['user'] = $user;
    }
}
