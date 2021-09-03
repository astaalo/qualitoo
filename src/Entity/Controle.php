<?php

namespace App\Entity;

use App\Entity\Absctract\NotificationInterface;
use App\Repository\ControleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ControleRepository::class)
 */
class Controle implements NotificationInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=45, nullable=true)
     */
    private $code;

    /**
     * @var integer
     * @ORM\Column(name="numero", type="integer", length=11, nullable=true)
     */
    private $numero;

    /**
     * @var String
     * @ORM\Column(name="description", type="text", length=4294967292, nullable=true)
     */
    protected $description;

    /**
     * @var String
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    protected $url;

    /**
     * @var boolean
     *
     * @ORM\Column(name="a_tester", type="boolean", nullable=true)
     */
    protected $aTester;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="datetime", nullable=true)
     */
    protected $dateCreation;

    /**
     * @var boolean
     * @ORM\Column(name="transfered", type="boolean", nullable=true)
     *
     */
    private $transfered=false;

    public function __construct() {
        $this->traitement = new ArrayCollection();
        $this->dateCreation = new \DateTime("NOW");
    }

    /**
     * @return number
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @param string $code
     * @return Controle
     */
    public function setCode($code) {
        $this->code = $code;
        return $this;
    }

    /**
     * @return integer
     */
    public function getNumero() {
        return $this->numero;
    }

    /**
     * @param integer $numero
     * @return Controle
     */
    public function setNumero($numero) {
        $this->numero = $numero;
        return $this;
    }



    /**
     * @return boolean
     */
    public function getEtat() {
        return $this->etat;
    }

    /**
     * @param integer $etat
     * @return Controle
     */
    public function setEtat($etat) {
        $this->etat = $etat;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Controle
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Niveau
     */
    public function getNiveau() {
        return $this->niveau;
    }

    /**
     * @param Niveau $niveau
     * @return Controle
     */
    public function setNiveau($niveau) {
        $this->niveau = $niveau;
        return $this;
    }

    /**
     * @return Utilisateur
     */
    public function getPorteur() {
        return $this->porteur;
    }

    /**
     * @param Utilisateur $porteur
     * @return Controle
     */
    public function setPorteur($porteur) {
        $this->structurePorteur = $porteur ? $porteur->getStructure() : null;
        $this->porteur = $porteur;
        return $this;
    }

    /**
     * @return Utilisateur
     */
    public function getSuperviseur() {
        return $this->superviseur;
    }

    /**
     * @param Utilisateur $superviseur
     * @return Controle
     */
    public function setSuperviseur($superviseur) {
        $this->structureSuperviseur = $superviseur->getStructure();
        $this->superviseur = $superviseur;
        return $this;
    }

    /**
     * @return MethodeControle
     */
    public function getMethodeControle() {
        return $this->methodeControle;
    }

    /**
     * @param MethodeControle $methode_controle
     * @return Controle
     */
    public function setMethodeControle($methodeControle) {
        $this->methodeControle = $methodeControle;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @param string $url
     * @return Controle
     */
    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getATester() {
        return $this->aTester;
    }

    /**
     * @param boolean $a_tester
     * @return Controle
     */
    public function setATester($aTester) {
        $this->aTester = $aTester;
        return $this;
    }


    /**
     * @return \Datetime
     */
    public function getDateCreation() {
        return $this->dateCreation;
    }

    /**
     * @param \DateTime $date_creation
     * @return Controle
     */
    public function setDateCreation($dateCreation) {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    /**
     * @return Periodicite
     */
    public function getPeriodicite() {
        return $this->periodicite;
    }

    /**
     * @param Periodicite $periodicite
     * @return Controle
     */
    public function setPeriodicite($periodicite) {
        $this->periodicite = $periodicite;
        return $this;
    }

    /**
     * @return TypeControle
     */
    public function getTypeControle() {
        return $this->typeControle;
    }

    /**
     * @param TypeControle $typeControle
     * @return Controle
     */
    public function setTypeControle($typeControle) {
        $this->typeControle = $typeControle;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getTraitement() {
        return $this->traitement;
    }

    /**
     * add traitement
     * @param Traitement $traitement
     * @return Controle
     */
    public function addTraitement($traitement) {
        $this->traitement->add($traitement);
        return $this;
    }

    /**
     * remove traitement
     * @param Traitement $traitement
     * @return Controle
     */
    public function removeTraitement($traitement) {
        $this->traitement->removeElement($traitement);
        return $this;
    }

    /**
     * @param Risque $risque
     * @return Controle
     */
    public function setRisque($risque) {
        $this->risque = $risque;
        return $this;
    }

    /**
     * get risque
     * @return Risque
     */
    public function getRisque() {
        return $this->risque ? $this->risque : ($this->causeOfRisque ? $this->causeOfRisque->getRisque() : null);
    }

    /**
     * get cause of risque
     * @return RisqueHasCause
     */
    public function getCauseOfRisque() {
        return $this->causeOfRisque;
    }

    /**
     * set cause of risque
     * @param RisqueHasCause $causeOfRisque
     * @return Controle
     */
    public function setCauseOfRisque(RisqueHasCause $causeOfRisque) {
        $this->causeOfRisque = $causeOfRisque;
        return $this;
    }

    /**
     * get cause
     * @return Cause
     */
    public function getCause() {
        return $this->causeOfRisque?$this->causeOfRisque->getCause():null;
    }

    /**
     * @return PlanAction
     */
    public function getPlanAction() {
        return $this->planAction;
    }
    /**
     * @return PlanAction
     */
    public function getToPlanAction() {
        return $this->toPlanAction;
    }

    /**
     * @param PlanAction $planAction
     * @return Controle
     */
    public function setPlanAction($planaction) {
        $this->planAction = $planaction;
        return $this;
    }


    /**
     * @return ArrayCollection
     */
    public function getQuiz() {
        return $this->quiz;
    }

    /**
     * @return ArrayCollection
     */
    public function getExecution() {
        return $this->execution;
    }

    /**
     * @return boolean
     */
    public function inValidation() {
        return in_array($this->getRisque()->getEtat(), array(Risque::$states['en_cours'], Risque::$states['a_valider'], Risque::$states['valide']));
    }

    /**
     * @return boolean
     */
    public function inIdentification() {
        return $this->getRisque()->getEtat()==Risque::$states['nouveau'];
    }

    /**
     * @return Controle
     */
    public function nextControle() {
        $data = $this->getRisque()->getControle();
        $index = 0;$isExist = false;
        foreach($data as $controle) {
            $index++;
            if($controle->getId()==$this->id) {
                $isExist = true;
                break;
            }
        }
        return $isExist ? $this->getRisque()->getControle()->get($index) : null;
    }

    /**
     * Get libelle
     * @return string
     */
    public function __toString(){
        return $this->description;
    }

    /* (non-PHPdoc)
     * @see Absctract\NotificationInterface::generateNotifications()
     */
    public function generateNotification(Utilisateur $user, TypeNotification $type, $isNew) {
        // TODO: Auto-generated method stub
        if($isNew) {
            $label = 'Enregistrement d\'un nouveau contrôle';
            $desc  = sprintf('Bonjour, <br> <b> %s </b> a enregistré un nouveau contrôle : <span style="color: #f60;">%s</span>, au risque <b>"%s"</b>"', $user, $this->getDescription(), $this->getRisque());
        } else {
            $label = 'Modification d\'un contrôle';
            $desc  = sprintf('Bonjour, <br> <b> %s </b> a modifié le contrôle : <span style="color: #f60;">%s</span>, du risque <b>"%s"</b>', $user, $this->getDescription(), $this->getRisque());
        }

        $notification = NotificationUtil::create($label, $desc, $this, $type, $user);
        if($this->getSuperviseur())
            $notification->addReceiver($this->getSuperviseur());
        if($this->getPorteur())
            $notification->addReceiver($this->getPorteur());

        $riskManagers = $this->getRisque()->getSociete()? $this->getRisque()->getSociete()->getRiskManager():null;
        foreach ($riskManagers as $riskManager) {
            $notification->addReceiver($riskManager);
        }

        return $notification;
    }

    /* (non-PHPdoc)
     * @see Absctract\NotificationInterface::generateWorkflows()
     */
    public function generateWorkflow(Notification $notification, TypeNotification $type, $isNew) {
        // TODO: Auto-generated method stub
        $mail = array();

        if($isNew) {
            $subject = 'Enregistrement d\'un nouveau contrôle';
            $action  = 'Nouveau contrôle';
            $content =  sprintf('Bonjour, <br> <b> %s </b> a enregistré un nouveau contrôle : <span style="color: #f60;">%s</span>, au risque <b>"%s"</b>', $notification->getUser(), $this->getDescription(), $this->getRisque());
        } else {
            $subject = 'Modification d\'un contrôle';
            $action  = 'Modification de contrôle';
            $content =  sprintf('Bonjour, <br> <b> %s </b> a modifié le contrôle : <span style="color: #f60;">%s</span>, du risque <b>"%s"</b>', $notification->getUser(), $this->getDescription(), $this->getRisque());
        }

        $mail['subject'] = $subject;
        $mail['body']['action'] = $action;
        $mail['body']['content'] = $content;
        $mail['body']['link'] = "https://sigr.orange-sonatel.com";
        $mail['recipients'] = array();

        $mail['recipients'][$notification->getUser()->getEmail()] = $notification->getUser()->__toString();

        foreach ($notification->getReceivers() as $receiver) {
            $mail['recipients'][$receiver->getEmail()] = $receiver->__toString();
        }

        return $mail;
    }


    /**
     * Set structurePorteur
     *
     * @param Structure $structurePorteur
     *
     * @return Controle
     */
    public function setStructurePorteur(Structure $structurePorteur = null)
    {
        $this->structurePorteur = $structurePorteur;

        return $this;
    }

    /**
     * Get structurePorteur
     *
     * @return Structure
     */
    public function getStructurePorteur()
    {
        return $this->structurePorteur;
    }

    /**
     * Set structureSuperviseur
     *
     * @param Structure $structureSuperviseur
     *
     * @return Controle
     */
    public function setStructureSuperviseur(Structure $structureSuperviseur = null)
    {
        $this->structureSuperviseur = $structureSuperviseur;

        return $this;
    }

    /**
     * Get structureSuperviseur
     *
     * @return Structure
     */
    public function getStructureSuperviseur()
    {
        return $this->structureSuperviseur;
    }

    /**
     * Set toPlanAction
     *
     * @param PlanAction $toPlanAction
     *
     * @return Controle
     */
    public function setToPlanAction(PlanAction $toPlanAction = null)
    {
        $this->toPlanAction = $toPlanAction;

        return $this;
    }

    /**
     * Add quiz
     *
     * @param Quiz $quiz
     *
     * @return Controle
     */
    public function addQuiz(Quiz $quiz)
    {
        $this->quiz[] = $quiz;

        return $this;
    }

    /**
     * Remove quiz
     *
     * @param Quiz $quiz
     */
    public function removeQuiz(Quiz $quiz)
    {
        $this->quiz->removeElement($quiz);
    }

    /**
     * Add execution
     *
     * @param Execution $execution
     *
     * @return Controle
     */
    public function addExecution(Execution $execution)
    {
        $this->execution[] = $execution;

        return $this;
    }

    /**
     * Remove execution
     *
     * @param Execution $execution
     */
    public function removeExecution(Execution $execution)
    {
        $this->execution->removeElement($execution);
    }

    /**
     * Set transfered
     *
     * @param boolean $transfered
     * @return Controle
     */
    public function setTransfered($transfered)
    {
        $this->transfered = $transfered;

        return $this;
    }

    /**
     * Get transfered
     *
     * @return boolean
     */
    public function getTransfered()
    {
        return $this->transfered;
    }



    /**
     * Set maturiteReel
     *
     * @param Maturite $maturiteReel
     * @return Controle
     */
    public function setMaturiteReel(Maturite $maturiteReel )
    {
        $this->maturiteReel = $maturiteReel;
        return $this;
    }

    /**
     * Get maturiteReel
     *
     * @return Maturite
     */
    public function getMaturiteReel()
    {
        return $this->maturiteReel;
    }

    /**
     * Set maturiteTheorique
     *
     * @param Maturite $maturiteTheorique
     * @return Controle
     */
    public function setMaturiteTheorique(Maturite $maturiteTheorique)
    {
        $this->maturiteTheorique = $maturiteTheorique;

        return $this;
    }

    /**
     * Get maturiteTheorique
     *
     * @return Maturite
     */
    public function getMaturiteTheorique()
    {
        return $this->maturiteTheorique;
    }

    /**
     * Set grille
     *
     * @param Grille $grille
     * @return controle
     */
    public function setGrille(Grille $grille = null)
    {
        $this->grille = $grille;

        return $this;
    }

    /**
     * Get grille
     *
     * @return Grille
     */
    public function getGrille()
    {
        return $this->grille;
    }

    /**
     * Add notificationControle
     *
     * @param NotificationControle $notificationControle
     *
     * @return Controle
     */
    public function addNotificationControle(NotificationControle $notificationControle)
    {
        $this->notificationControle[] = $notificationControle;

        return $this;
    }

    /**
     * Remove notificationControle
     *
     * @param NotificationControle $notificationControle
     */
    public function removeNotificationControle(NotificationControle $notificationControle)
    {
        $this->notificationControle->removeElement($notificationControle);
    }

    /**
     * Get notificationControle
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotificationControle()
    {
        return $this->notificationControle;
    }
}
