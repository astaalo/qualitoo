<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Preuve
 *
 * @ORM\Table(name="preuve")
 * @ORM\Entity
 */
class Preuve
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
     * @var \DateTime
     *
     * @ORM\Column(name="date_ajout", type="datetime", nullable=false)
     */
    private $dateAjout;

    /**
     * @var string
     *
     * @ORM\Column(name="fichier", type="string", length=255, nullable=false)
     */
    private $fichier;

    /**
     * @var boolean
     *
     * @ORM\Column(name="valide", type="boolean", nullable=true)
     */
    private $valide;

    /**
     * @var Execution
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Execution")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="execution_id", referencedColumnName="id")
     * })
     */
    private $execution;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dateAjout
     *
     * @param \DateTime $dateAjout
     * @return Preuve
     */
    public function setDateAjout($dateAjout)
    {
        $this->dateAjout = $dateAjout;

        return $this;
    }

    /**
     * Get dateAjout
     *
     * @return \DateTime 
     */
    public function getDateAjout()
    {
        return $this->dateAjout;
    }

    /**
     * Set fichier
     *
     * @param string $fichier
     * @return Preuve
     */
    public function setFichier($fichier)
    {
        $this->fichier = $fichier;

        return $this;
    }

    /**
     * Get fichier
     *
     * @return string 
     */
    public function getFichier()
    {
        return $this->fichier;
    }

    /**
     * Set valide
     *
     * @param boolean $valide
     * @return Preuve
     */
    public function setValide($valide)
    {
        $this->valide = $valide;

        return $this;
    }

    /**
     * Get valide
     *
     * @return boolean 
     */
    public function getValide()
    {
        return $this->valide;
    }

    /**
     * Set execution
     *
     * @param Execution $execution
     * @return Preuve
     */
    public function setExecution(Execution $execution = null)
    {
        $this->execution = $execution;

        return $this;
    }

    /**
     * Get execution
     *
     * @return Execution
     */
    public function getExecution()
    {
        return $this->execution;
    }
}
