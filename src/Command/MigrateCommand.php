<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Orange\QuickMakingBundle\EventListener\LogsListener;
use MongoDB\Client;

class MigrateCommand extends ContainerAwareCommand
{
	
	protected function configure()
	{
		$this->setName('sigrc:migrate')
		     ->setDescription('Migrer les risques vers la base de reporting');
	}
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Symfony\Component\Console\Command\Command::execute()
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('mongo.native_long', 0);
		$em = $this->getContainer()->get('doctrine.orm.entity_manager');
		$evm = $em->getEventManager();
		foreach ($em->getEventManager()->getListeners() as $listeners) {
			foreach($listeners as $listener) {
				if($listener instanceof LogsListener) {
					$evm->removeEventListener(array('preUpdate', 'postFlush'), $listener);
				}
			}
		}
		$dm = $this->getContainer()->get('doctrine_mongodb.odm.default_connection');
		$db = $dm->selectDatabase('coris');
		$collectionR = $db->createCollection('risque');
		$collectionE = $db->createCollection('evaluation');
		$risques = $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository('OrangeMainBundle:Risque')->findToMigrate();
		$index = 0;
		foreach($risques as $risque) {
			$data = $risque->showValuesAsToMigrate();
			$risque->setTobeMigrate(false);
			if($data==null) {
				$em->persist($risque);
				continue;
			}
			//$collectionE->remove(array('risque'=>$risque->getId()));
			foreach($risque->showEvaluationAsToMigrate() as $year => $values) {
				$collectionE->update(array('risque'=>$risque->getId(), 'annee' => $year), array('$set' => $values), array('upsert'=>true));
			}
			$collectionR->update(array('risque'=>$risque->getId()), array('$set' => $data), array('upsert'=>true));
			$em->persist($risque);
			$index += 1;
			if($index==11) {
				$index = 1;
			}
		}
		$em->flush();
		$output->writeln(utf8_encode('La commande s\'executée correctement!'));
	}
}