<?php

namespace App\Entity;

use App\Repository\PlanActionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Absctract\NotificationInterface;
use App\Utils\NotificationUtil;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=PlanActionRepository::class)
 */
class PlanAction implements NotificationInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
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
     *
     * @ORM\Column(name="numero", type="integer", length=11, nullable=true)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=2000, nullable=false)
     * @Assert\NotNull(message="Veuillez saisir le libellé du plan d'action")
     */
    private $libelle;

    /**
     * @var string
     * @ORM\Column(name="nom_porteur", type="string", length=255, nullable=true)
     */
    private $nom_porteur;

    /**
     * @var \Datetime
     *
     * @ORM\Column(name="date_debut", type="date", nullable=true)
     */
    private $dateDebut;

    /**
     * @var \Datetime
     */
    public $dateDebutFrom;


    /**
     * @var \Datetime
     */
    public $dateDebutTo;

    /**
     * @var \Datetime
     *
     * @ORM\Column(name="date_fin", type="date", nullable=true)
     * @Assert\NotNull(message="La date de fin est obligatoire", groups={"Validation"})
     */
    private $dateFin;

    /**
     * @var \Datetime
     */
    public $dateFinFrom;


    /**
     * @var \Datetime
     */
    public  $dateFinTo;

    

    /**
     * @var boolean
     *
     * @ORM\Column(name="etat", type="boolean", nullable=true)
     */
    private $etat = true;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Assert\NotNull(message="Veuillez saisir la description", groups={"Validation"})
     */
    private $description;

    /**
     * @var Processus
     */
    public $processus;

    /**
     * @var Structure
     */
    public $structure;

    /**
     * @var boolean
     * @ORM\Column(name="transfered", type="boolean", nullable=true)
     *
     */
    private $transfered=false;

    public function __construct() {
        $this->avancement = new \Doctrine\Common\Collections\ArrayCollection();
    }


    public function getId() {
        return $this->id;
    }

    public function setId() {
        $this->id=null;
        return $this;
    }

    public function getCode() {
        return $this->code;
    }

    /**
     * @param string $code
     * @return PlanAction
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
     * @return PlanAction
     */
    public function setNumero($numero) {
        $this->numero = $numero;
        return $this;
    }

    public function getLibelle() {
        return $this->libelle;
    }

    /**
     * @return \DateTime
     */
    public function getDateDebut() {
        return $this->dateDebut;
    }

    /**
     * @param \Datetime $dateDebut
     * @return PlanAction
     */
    public function setDateDebut($dateDebut) {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    /**
     * @return \Datetime
     */
    public function getDateFin() {
        return $this->dateFin;
    }

    /**
     * @param \Datetime $dateFin
     * @return PlanAction
     */
    public function setDateFin($dateFin) {
        $this->dateFin = $dateFin;
        return $this;
    }

    /**
     * get description
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * set description
     * @param string $description
     * @return PlanAction
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getEtat() {
        return $this->etat;
    }

    /**
     * set etat
     * @param boolean $etat
     * @return PlanAction
     */
    public function setEtat($etat) {
        $this->etat = $etat;
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
     * @return PlanAction
     */
    public function setPorteur($porteur) {
        $this->struturePorteur = $porteur->getStructure();
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
     * @return PlanAction
     */
    public function setSuperviseur($superviseur) {
        $this->struturePorteur = $superviseur->getStructure();
        $this->superviseur = $superviseur;
        return $this;
    }

    /**
     * get statut
     * @return Statut
     */
    public function getStatut() {
        return $this->statut;
    }

    /**
     * set statut
     * @param Statut $statut
     * @return PlanAction
     */
    public function setStatut($statut) {
        $this->statut = $statut;
        return $this;
    }

    public function inValidation() {
        return in_array($this->getRisque()->getEtat(), array(Risque::$states['en_cours'], Risque::$states['a_valider'], Risque::$states['valide']));
    }

    public function inIdentification() {
        return $this->getRisque()->getEtat()==Risque::$states['nouveau'];
    }

    /**
     * @return PlanAction
     */
    public function nextPlanAction() {
        $data = $this->getRisque()->getPlanAction();
        $index = 0;$isExist = false;
        foreach($data as $planAction) {
            $index++;
            if($planAction->getId()==$this->id) {
                $isExist = true;
                break;
            }
        }
        return $isExist ? $this->getRisque()->getPlanAction()->get($index) : null;
    }

    /**
     * get libelle
     * @return string
     */
    public function __toString() {
        return $this->libelle;
    }

    /* (non-PHPdoc)
     * @see Absctract\NotificationInterface::generateNotification()
     */
    public function generateNotification(Utilisateur $user, TypeNotification $type, $isNew) {
        // TODO: Auto-generated method stub
        $label = 'Enregistrement d\'une action';
        $desc  = sprintf('Bonjour, <br> <b> %s </b> a enregistré une nouvelle action : <span style="color: #f60;">%s</span>, au risque <b>"%s"</b>"', $user, $this->getLibelle(), $this->getRisque());

        $notification = NotificationUtil::create($label, $desc, $this, $type, $user);
        if($this->getSuperviseur()) {
            $notification->addReceiver($this->getSuperviseur());
        }
        if($this->getPorteur()) {
            $notification->addReceiver($this->getPorteur());
        }

        $riskManagers = $this->getRisque()->getSociete()? $this->getRisque()->getSociete()->getRiskManager():null;
        foreach ($riskManagers as $riskManager) {
            $notification->addReceiver($riskManager);
        }

        return $notification;
    }

    /* (non-PHPdoc)
     * @see Absctract\NotificationInterface::generateWorkflow()
     */
    public function generateWorkflow(Notification $notification, TypeNotification $type, $isNew) {
        // TODO: Auto-generated method stub
        $mail = array();

        $subject = 'Enregistrement d\'une action';
        $action = 'Nouvelle action';
        $content = sprintf('Bonjour, <br> <b> %s </b> a enregistré une nouvelle action : <span style="color: #f60;">%s</span>, au risque <b>"%s"</b>', $notification->getUser(), $this->getLibelle(), $this->getRisque());

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
     * @return PlanAction
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
     * @return PlanAction
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
     * Set toControle
     *
     * @param Controle $toControle
     *
     * @return PlanAction
     */
    public function setToControle(Controle $toControle = null)
    {
        $this->toControle = $toControle;

        return $this;
    }

    /**
     * Add avancement
     *
     * @param Avancement $avancement
     *
     * @return PlanAction
     */
    public function addAvancement(Avancement $avancement)
    {
        $this->avancement[] = $avancement;

        return $this;
    }

    /**
     * Remove avancement
     *
     * @param Avancement $avancement
     */
    public function removeAvancement(Avancement $avancement)
    {
        $this->avancement->removeElement($avancement);
    }

    /**
     * Set transfered
     *
     * @param boolean $transfered
     * @return PlanAction
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
     * Set nomPorteur
     *
     * @param string $nomPorteur
     *
     * @return PlanAction
     */
    public function setNomPorteur($nomPorteur)
    {
        $this->nom_porteur = $nomPorteur;

        return $this;
    }

    /**
     * Get nomPorteur
     *
     * @return string
     */
    public function getNomPorteur()
    {
        return $this->nom_porteur;
    }

}
