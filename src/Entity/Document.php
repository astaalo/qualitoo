<?php
namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\DocumentRepository;

/**
 * Document
 * @ORM\Table(name="document")
 * @ORM\Entity(repositoryClass=DocumentRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Document
{
	
	static $motif;
	
	/**
	 * @var integer
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="libelle", type="string", length=100, nullable=false)
	 */
	private $libelle;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="nom_fichier", type="string", length=100, nullable=true)
	 */
	private $nomFichier;

	/**
	 * @var \DateTime
	 * @ORM\Column(name="date_creation", type="datetime", nullable=false)
	 */
	private $dateCreation;
	
	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="etat", type="boolean", nullable=true)
	 */
	private $etat = true;
	
	/**
	 * @var String
	 *
	 * @ORM\Column(name="description", type="text", nullable=true)
	 */
	protected $description;
	
	/**
	 * @var Utilisateur
	 * @ORM\ManyToOne(targetEntity="Utilisateur")
	 * @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="utilisateur", referencedColumnName="id")
	 * })
	 */
	private $utilisateur;
	
	/**
	 * @var TypeDocument
	 * @ORM\ManyToOne(targetEntity="TypeDocument")
	 * @ORM\JoinColumn(name="type_document_id", referencedColumnName="id", nullable=true)
	 */
	private $typeDocument;

    
    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(message="Merci de joindre un fichier.")
     * @Assert\File(mimeTypes={ "application/pdf" , "application/msword", "application/vnd.ms-powerpoint", "application/vnd.ms-excel"})
     */
     public $file;
     
     /**
      * @var boolean
      * @ORM\Column(name="deleted", type="boolean", nullable=false)
      */
     private $deleted;
     

     /**
      * @ORM\ManyToMany(targetEntity=Structure::class, inversedBy="documents")
      */
     private $document;
     
	/**
	 * Constructor
	 */
	public function __construct() {
                  	    $this->dateCreation = new \DateTime();
                  	    $this->deleted = false;
                       $this->document = new ArrayCollection();
                  	}
	
	public function __toString(){
                  		return $this->libelle;
                  	}
	/**
	 * Get id
	 * @return integer
	 */
	public function getId()
                  	{
                  		return $this->id;
                  	}

	/**
	 * Set nomFichier
	 * @param string $nomFichier
	 * @return Document
	 */
	public function setNomFichier($nomFichier) {
                  		$this->nomFichier = $nomFichier;
                  		return $this;
                  	}

	/**
	 * Set libelle
	 * @param string $libelle
	 * @return Document
	 */
	public function setLibelle($libelle) {
                  		$this->libelle = $libelle;
                  		return $this;
                  	}

	/**
	 * Get libelle
	 * @return string
	 */
	public function getLibelle() {
                  		return $this->libelle;
                  	}

	/**
	 * Set dateCreation
	 * @param \DateTime $dateCreation
	 * @return Document
	 */
	public function setDateCreation($dateCreation) {
                  		$this->dateCreation = $dateCreation;
                  		return $this;
                  	}

	/**
	 * Get dateCreation
	 * @return \DateTime
	 */
	public function getDateCreation() {
                  		return $this->dateCreation;
                  	}
	
	/**
	 * @return Utilisateur
	 */
	public function getUtilisateur() {
                  		return $this->utilisateur;
                  	}
	
	/**
	 * @param Utilisateur $utilisateur
	 * @return Document
	 */
	public function setUtilisateur($utilisateur) {
                  		$this->utilisateur = $utilisateur;
                  		return $this;
                  	}
	

    public function getAbsolutePath() {
        return null === $this->file ? null : $this->getUploadRootDir().'/'.$this->file;
    }

    public function getPath() {
        return $this->getWebPath();
    }

    public function getWebPath() {
        return null === $this->file ? null : $this->getUploadDir().'/'.$this->file;
    }

    /**
     * @return string
     */
    protected function getUploadRootDir() {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir() {
        return 'upload/sharepoint';
    }
    
//     /**
//      * @ORM\PrePersist()
//      * @ORM\PreUpdate()
//      */
//     public function preUpload() {
//         if (null !== $this->file) {
//     		$this->nomFichier = $this->file->getClientOriginalName();
//         }
//     }

//     /**
//      * @ORM\PostPersist()
//      * @ORM\PostUpdate()
//      */
//     public function upload() {
//         if (null === $this->file) {
//             return;
//         }
//        $this->nomFichier = $this->file->getClientOriginalName();
//     }
    
//     /**
//      * @ORM\PostRemove()
//      */
//     public function removeUpload() {
//     	$file = $this->getAbsolutePath();
//         if(file_exists($file)) {
//             unlink($file);
//         }
//     }
	public function getTypeDocument() {
                  		return $this->typeDocument;
                  	}
	public function setTypeDocument($typeDocument) {
                  		$this->typeDocument = $typeDocument;
                  		return $this;
                  	}
	public function getDescription() {
                  		return $this->description;
                  	}
	public function setDescription($description) {
                  		$this->description = $description;
                  		return $this;
                  	}
	public function getNomFichier() {
                  		return $this->nomFichier;
                  	}
	public function getFile() {
                  		return $this->file;
                  	}
	public function setFile($file) {
                  		$this->file = $file;
                  		return $this;
                  	}
	public function getEtat() {
                  		return $this->etat;
                  	}
	public function setEtat($etat) {
                  		$this->etat = $etat;
                  		return $this;
                  	}

    /**
     * Add utilisateursAutorises
     *
     * @param DocumentHasUtilisateur $utilisateursAutorises
     * @return Document
     */
    public function addUtilisateursAutorise(DocumentHasUtilisateur $utilisateursAutorises)
    {
        $this->utilisateursAutorises[] = $utilisateursAutorises;
    
        return $this;
    }

    /**
     * Remove utilisateursAutorises
     *
     * @param DocumentHasUtilisateur $utilisateursAutorises
     */
    public function removeUtilisateursAutorise(DocumentHasUtilisateur $utilisateursAutorises)
    {
        $this->utilisateursAutorises->removeElement($utilisateursAutorises);
    }

    /**
     * Get utilisateursAutorises
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUtilisateursAutorises()
    {
        return $this->utilisateursAutorises;
    }
    
    /**
     * Get tmpUtilisateur
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTmpUtilisateur() {
    	$this->tmpUtilisateur = new \Doctrine\Common\Collections\ArrayCollection();
    	foreach($this->utilisateursAutorises as $user) {
    		$this->tmpUtilisateur->add($user->getUtilisateur());
    	}
    	return $this->tmpUtilisateur;
    }
    
    /**
     * @param Utilisateur $tmp_user
     * @return Document
     */
    public function addTmpUtilisateur($tmp_user) {
    	$this->tmpUtilisateur->add($tmp_user);
    	$isExist=false;
    	foreach ($this->utilisateursAutorises as $user){
    		if($user->getUtilisateur()->getId()==$tmp_user->getId()) {
    			$isExist=true;
    			break;
    		}
    	}
    	if ($isExist==false) {
    		$anim= new DocumentHasUtilisateur();
    		$anim->setDocument($this);
    		$anim->setUtilisateur($tmp_user);
    		$this->utilisateursAutorises->add($anim);
    	}
    	return $this;
    }
    
    /**
     * @param Utilisateur $tmp_user
     * @return Document
     */
    public function removeTmpUtilisateur($tmp_user) {
    	foreach ($this->utilisateursAutorises as $user){
    		if($user->getUtilisateur()->getId()==$tmp_user->getId()) {
    			$this->removeUtilisateursAutorise($user);
    			break;
    		}
    	}
    	return $this;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Document
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    
        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @return Collection|Structure[]
     */
    public function getDocument(): Collection
    {
        return $this->document;
    }

    public function addDocument(Structure $document): self
    {
        if (!$this->document->contains($document)) {
            $this->document[] = $document;
        }

        return $this;
    }

    public function removeDocument(Structure $document): self
    {
        $this->document->removeElement($document);

        return $this;
    }
}
