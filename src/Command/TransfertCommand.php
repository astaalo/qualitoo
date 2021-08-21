<?php

namespace App\Command;  

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class TransfertCommand extends ContainerAwareCommand
{
	
	protected function configure()
	{
		$this->setName('sigrc:transfert')
		     ->setDescription('Relancer les utilisateurs à l\'approche des échéances des actions. ');
	}
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Symfony\Component\Console\Command\Command::execute()
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$em = $this->getContainer()->get('doctrine.orm.entity_manager');
		$risks=$em->getRepository(Risque::class)->findBy(array('first'=>null));
		foreach ($risks as $risk){
			$risksCommuns= $em->getRepository(Risque::class)->getDoublons($risk)->getQuery()->execute();
			if(count($risksCommuns)>1){
				
			}else{
				$risk->setFirst(true);
				$em->persist($risk);
				$em->flush();
			}
			
		}
		$output->writeln(utf8_encode('La commande s\'executée correctement!'));
	}
}