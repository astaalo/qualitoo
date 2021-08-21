<?php
namespace App\Command;

use App\Entity\Relance;
use App\Entity\Repetition;
use App\Entity\Risque;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

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
		$relances= $em->getRepository(Relance::class)->findBy(array('isActif'=> true));
		$states_risk = $this->getContainer()->getParameter('states')['risque'];
		foreach ($relances as $relance){
			$riksManager = $relance->getSociete()->getRiskManager();
			$risques = $em->getRepository(Risque::class)->findBy(array(
				'etat' => array($states_risk['nouveau'], $states_risk['en_cours']),
				'societe' => $relance->getSociete(),
				'relanced' => true
			));
			foreach ($risques as $risk) {
				$repetitionsOfRisk = $em->getRepository(Repetition::class)->getLastRepetition($risk);
				$dateValidOrSaisie = $risk->isPending() ? $risk->getDateValidation():$risk->getDateSaisie();
				$lastRepetition = !empty($repetitionsOfRisk) ? $repetitionsOfRisk[0] : null;
				$lastDateRepetition = $lastRepetition == null ? $dateValidOrSaisie : $lastRepetition->getDateRepetition();
				$debutRelanceRisk = $this->getDebutRelance($relance, $lastDateRepetition);
				if (strtotime($debutRelanceRisk) <= $today->getTimestamp()) { // verifier si les relances doivent commencer
					$dateOfNextRepetitionToSeconds = $this->getNextDateRepitition($relance, $lastDateRepetition);
					if (strtotime($dateOfNextRepetitionToSeconds) <= $today->getTimestamp()) { //verifie si la frequence de repetition est atteinte
						$newRepetition= new Repetition();
						$newRepetition->setRelance($relance);
						$newRepetition->setRisque($risk);
						$em->persist($newRepetition);
						//envoie de mail 
						$array_mails=array();
						$mail=array();
						foreach ($riksManager as $rm) { $array_mails[]=$rm->getEmail(); }
						$mail['subject'] = "Relance sur la validation des actions";
						$mail['body']['action'] = "Relance";
						$mail['body']['content'] = sprintf('Bonjour <b>RISK MANAGERS</b>, <br>  Vous etes invités à valider ce risque : <span style="color: #f60;">%s</span>', $risk );
						$mail['body']['link'] = 'https://coris.orange-sonatel.com';
						$mail['body']['linkvalidation'] = 'https://coris.orange-sonatel.com'.$this->getContainer()->get('router')->generate('validation_risque',array('id' => $risk->getId()));
						$mail['recipients'] = $array_mails;
						$mailer->sendArray($mail);
						// $em->flush();
					}
				}
			}
		}
		$output->writeln(utf8_encode('La commande s\'executée correctement!'));
	}

	public function getDebutRelance($relance, $lastDateRepet) {
		return date('d-m-Y H:i:s', strtotime('+'.$relance->getNbDebut().' '.$relance->getUniteTpsDebut()->getCode(), $lastDateRepet->getTimestamp()));
	}

	public function getNextDateRepitition($relance, $lastDateRepet) {
		return date('d-m-Y H:i:s', strtotime('+'.$relance->getNbFrequence().' '.$relance->getUniteTpsFrequence()->getCode(), $lastDateRepet->getTimestamp()));
	}
}