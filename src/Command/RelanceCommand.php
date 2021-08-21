<?php
namespace App\Command;  

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use App\Entity\Repetition;

class RelanceCommand extends ContainerAwareCommand
{
	
	protected function configure()
	{
		$this->setName('sigrc:relance')
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
		$mailer=$this->getContainer()->get('orange_main.mailer');
		$today=new \DateTime("NOW");
		$relances= $em->getRepository('OrangeMainBundle:Relance')->findBy(array('isActif'=> true));
		foreach ($relances as $relance){
			$risques=$em->getRepository('OrangeMainBundle:Risque')
			            ->findBy(array('etat'=> array($this->getContainer()->getParameter('states')['risque']['nouveau'], $this->getContainer()->getParameter('states')['risque']['en_cours']), 
			            			   'societe'=>$relance->getSociete(),
			            			   'relanced'=>true 
			            ));
			            		
			$riksManager=$relance->getSociete()->getRiskManager();
			foreach ($risques as $risk){
				$repetitionsOfRisk=$em->getRepository('OrangeMainBundle:Repetition')->getLastRepetition($risk);
				$lastRepetition = count($repetitionsOfRisk)>0
								? $repetitionsOfRisk[0]
								: null;
				$lastDateRepetition = ($lastRepetition==null)
								    ? ($risk->isPending() ? $risk->getDateValidation():$risk->getDateSaisie()) 
								    : $lastRepetition->getDateRepetition();
				$debutRelanceRisk=date('d-m-Y H:i:s', strtotime('+'.$relance->getNbDebut().' '.$relance->getUniteTpsDebut()->getCode(), $lastDateRepetition->getTimestamp()));
				if(strtotime($debutRelanceRisk)<=$today->getTimestamp()){ // verifier si les relances doivent commencer
					$dateOfNextRepetitionToSeconds = date('d-m-Y H:i:s', strtotime('+'.$relance->getNbFrequence().' '.$relance->getUniteTpsFrequence()->getCode(), $lastDateRepetition->getTimestamp()));
					if(strtotime($dateOfNextRepetitionToSeconds)<=$today->getTimestamp()){//verifie si la frequence de repetition est atteinte
						$newRepetition= new Repetition();
						$newRepetition->setRelance($relance);
						$newRepetition->setRisque($risk);
						$em->persist($newRepetition);
						//envoie de mail 
						$array_mails=array();
						$mail=array();
						foreach ($riksManager as $rm)
							$array_mails[]=$rm->getEmail();
						
						$mail['subject'] = "Relance sur la validation des actions";
						$mail['body']['action'] = "Relance";
						$mail['body']['content'] = sprintf('Bonjour <b>RISK MANAGERS</b>, <br>  Vous etes invités à valider ce risque : <span style="color: #f60;">%s</span>', $risk );
						$mail['body']['link'] = 'https://sigr.orange-sonatel.com';
						$mail['body']['linkvalidation'] = 'https://sigr.orange-sonatel.com'.$this->getContainer()->get('router')->generate('validation_risque',array('id' => $risk->getId()));
						$mail['recipients'] = $array_mails;
						$mailer->sendArray($mail);
					///	$em->flush();
					}
				}
			}
		}
		$output->writeln(utf8_encode('La commande s\'executée correctement!'));
	}
}