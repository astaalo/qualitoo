<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * RisqueHasCause
 *
 * @ORM\Table(name="document_has_utilisateur")
 * @ORM\Entity()
 */
class DocumentHasUtilisateur
{
	/**
	 * @var integer
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 */
	private $id;
	
    /**
     * @var Risque
     *
     * @ORM\ManyToOne(targetEntity="Document")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="document_id", referencedColumnName="id")
     * })
     */
    private $document;
    

    /**
     * @var Cause
     * @ORM\ManyToOne(targetEntity="Utilisateur", cascade={"persist"})
     * @ORM\JoinColumns({
     * 	@ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
     * })
     */
    private $utilisateur;
    
    
    public function __construct() {
    }
    
    public function getId() {
    	return $this->id;
    }
    
    public function __toString() {
    	return $this->utilisateur->__toString();
    }
    
    

    /**
     * Set document
     *
     * @param Document $document
     * @return DocumentHasUtilisateur
     */
    public function setDocument(Document $document = null)
    {
        $this->document = $document;
    
        return $this;
    }

    /**
     * Get document
     *
     * @return Document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set utilisateur
     *
     * @param Utilisateur $utilisateur
     * @return DocumentHasUtilisateur
     */
    public function setUtilisateur(Utilisateur $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;
    
        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return Utilisateur
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }
}
