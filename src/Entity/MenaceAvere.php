<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MenaceAvere
 *
 * @ORM\Table(name="menace_avere")
 * @ORM\Entity()
 */
class MenaceAvere
{
	/**
	 * @var integer
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 */
	private $id;
	
    /**
     * @var Menace
     *
     * @ORM\ManyToOne(targetEntity="Menace")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="menace_id", referencedColumnName="id")
     * })
     */
    private $menace;

    /**
     * @var PeriodeAvere
     * @ORM\ManyToOne(targetEntity="PeriodeAvere")
     * @ORM\JoinColumns({
     * 	@ORM\JoinColumn(name="periode_id", referencedColumnName="id")
     * })
     */
    private $periode;
    
    
    /**
     * @var \Datetime
     * @ORM\Column(name="date_ajout", type="date", nullable=false)
     *
     */
    private $dateAjout;

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
     * @return RisqueAvere
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
     * Set periode
     *
     * @param PeriodeAvere $periode
     * @return RisqueAvere
     */
    public function setPeriode(PeriodeAvere $periode = null)
    {
        $this->periode = $periode;
    
        return $this;
    }

    /**
     * Get periode
     *
     * @return PeriodeAvere
     */
    public function getPeriode()
    {
        return $this->periode;
    }

    /**
     * Set menace
     *
     * @param Menace $menace
     * @return MenaceAvere
     */
    public function setMenace(Menace $menace = null)
    {
        $this->menace = $menace;
    
        return $this;
    }

    /**
     * Get menace
     *
     * @return Menace
     */
    public function getMenace()
    {
        return $this->menace;
    }
}
