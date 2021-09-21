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
	 * @var TypeDocument
	 * @ORM\ManyToOne(targetEntity="TypeDocument")
	 * @ORM\JoinColumn(name="type_document_id", referencedColumnName="id", nullable=true)
	 */
	private $typeDocument;
    
    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(message="Merci de joindre un fichier.")
     * @Assert\File(mimeTypes={ "application/pdf,"})
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
      * @ORM\ManyToOne(targetEntity=Rubrique::class, inversedBy="documents")
      */
     private $rubrique;

     /**
      * @ORM\ManyToOne(targetEntity=Theme::class, inversedBy="documents")
      */
     private $theme;

     /**
      * @ORM\ManyToOne(targetEntity=Profil::class, inversedBy="documents")
      */
     private $profil;
 
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
	public function getId(){
                 return $this->id;
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

    public function getRubrique(): ?Rubrique
    {
        return $this->rubrique;
    }

    public function setRubrique(?Rubrique $rubrique): self
    {
        $this->rubrique = $rubrique;
        return $this;
    }

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(?Theme $theme): self
    {
        $this->theme = $theme;
        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): self
    {
        $this->profil = $profil;

        return $this;
    }
}
