<?php

namespace App\Entity;

use App\Repository\ProjetRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProjetRepository::class)
 */
class Projet
{
    static $states;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero", type="integer", length=11, nullable=true)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=50, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=100, nullable=false)
     * @Assert\NotBlank(message="Veuillez saisir le libellé")
     */
    private $libelle;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_debut", type="datetime", nullable=false)
     * @Assert\NotBlank(message="Veuillez saisir la date de début")
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_fin", type="datetime", nullable=false)
     * @Assert\NotBlank(message="Veuillez saisir la date de fin")
     */
    private $dateFin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_cloture", type="datetime", nullable=true)
     */
    private $dateCloture;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     * @Assert\NotNull(message="Veuillez saisir la description")
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="etat",  columnDefinition="TINYINT(1) default 0")
     */
    private $etat = 0;

    


    


    /**
     * Get id
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set numero
     * @param integer $numero
     * @return Projet
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
        return $this;
    }

    /**
     * Get numero
     * @return integer
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set code
     * @param string $code
     * @return Projet
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get libelle
     * @return string
     */
    public function getLibelle() {
        return $this->libelle;
    }

    /**
     * Set libelle
     * @param string $libelle
     * @return Projet
     */
    public function setLibelle($libelle) {
        $this->libelle = $libelle;
        return $this;
    }

    /**
     * Get date debut
     * @return \DateTime
     */
    public function getDateDebut() {
        return $this->dateDebut;
    }

    /**
     * Set date debut
     * @param \DateTime $dateDebut
     * @return Projet
     */
    public function setDateDebut($dateDebut) {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    /**
     * get date fin
     * @return \DateTime
     */
    public function getDateFin() {
        return $this->dateFin;
    }

    /**
     * set date fin
     * @param \DateTime $dateFin
     * @return Projet
     */
    public function setDateFin($dateFin) {
        $this->dateFin = $dateFin;
        return $this;
    }

    /**
     * get date cloture
     * @return \DateTime
     */
    public function getDateCloture() {
        return $this->dateCloture;
    }

    /**
     * Set date cloture
     * @param \DateTime $dateCloture
     * @return Projet
     */
    public function setDateCloture($dateCloture) {
        $this->dateCloture = $dateCloture;
        return $this;
    }

    /**
     * Get description
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set description
     * @param string $description
     * @return Projet
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * Get utilisateur
     * @return Utilisateur
     */
    public function getUtilisateur() {
        return $this->utilisateur;
    }

    /**
     * Set utilisateur
     * @param Utilisateur $utilisateur
     * @return Projet
     */
    public function setUtilisateur($utilisateur) {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    /**
     * Get processus
     * @return Processus
     */
    public function getProcessus() {
        return $this->processus;
    }

    /**
     * Set processus
     * @param Processus $processus
     * @return Projet
     */
    public function setProcessus($processus) {
        $this->processus = $processus;
        return $this;
    }

    /**
     * Get societe
     * @return Societe
     */
    public function getSociete() {
        return $this->societe;
    }

    /**
     * Set societe
     * @param Societe $societe
     * @return Projet
     */
    public function setSociete($societe) {
        $this->societe= $societe;
        return $this;
    }

    /**
     * Set etat
     * @param integer $etat
     * @return Projet
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;
        return $this;
    }

    /**
     * Get etat
     * @return integer
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * get libelle
     * @return string
     */
    public function __toString() {
        return $this->libelle;
    }
}