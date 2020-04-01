<?php

namespace App\Entity;

use App\Entity\Trick;
use App\Entity\Comment;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *  fields={"username"},
 *  message="Ce nom d'utilisateur est déjà utilisé"
 * )
 * @UniqueEntity(
 *  fields={"email"},
 *  message="Cet email est déjà utilisé"
 * )
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Veuillez renseigner un nom d'utilisateur")
     * @Assert\Length(max=50, maxMessage="Votre nom d'utilisateur ne doit pas dépasser 50 caractères")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\Email(message="Veuillez renseigner un email valide")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=8, minMessage="Votre mot de passe doit faire au moins 8 caractères !")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="La confirmation et le mot de passe ne correspondent pas !")
     */
    private $passwordConfirm;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Trick", mappedBy="user", orphanRemoval=true)
     */
    private $tricks;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user", orphanRemoval=true)
     */
    private $comments;

   /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reset_token;
    /**
     * @ORM\Column(type="boolean")
     */
    private $activated;

    /**
     * @Assert\Image(
     *  mimeTypes= {"image/jpeg", "image/jpg", "image/png"},
     *  mimeTypesMessage = "Le fichier ne possède pas une extension valide ! Veuillez insérer une image en .jpg, .jpeg ou .png",
     *  minWidth = 24,
     *  minWidthMessage = "La largeur de cette image est trop petite",
     *  maxWidth = 2000,
     *  maxWidthMessage = "La largeur de cette image est trop grande",
     *  minHeight = 24,
     *  minHeightMessage = "La hauteur de cette image est trop petite",
     *  maxHeight = 2000,
     *  maxHeightMessage ="La hauteur de cette image est trop grande",
     *  minRatio = 1,
     *  minRatioMessage = "L'image doit être carré c-à-d un ratio de 1:1",
     *  maxRatio = 1,
     *  maxRatioMessage = "L'image doit être carré c-à-d un ratio de 1:1"
     *  )
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $imagePath;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $imageName;

     /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $token;

    public function __construct()
    {
        $this->tricks = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPasswordConfirm(): ?string
    {
        return $this->passwordConfirm;
    }

    public function setPasswordConfirm(string $passwordConfirm): self
    {
        $this->passwordConfirm = $passwordConfirm;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection|Trick[]
     */
    public function getTricks(): Collection
    {
        return $this->tricks;
    }

    public function addTrick(Trick $trick): self
    {
        if (!$this->tricks->contains($trick)) {
            $this->tricks[] = $trick;
            $trick->setUser($this);
        }

        return $this;
    }

    public function removeTrick(Trick $trick): self
    {
        if ($this->tricks->contains($trick)) {
            $this->tricks->removeElement($trick);
            // set the owning side to null (unless already changed)
            if ($trick->getUser() === $this) {
                $trick->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    public function getRoles() {
        $roles = $this->roles;
        
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getsalt() {
        return null;
    }

    public function eraseCredentials() {}

    
    public function getActivated(): ?bool
    {
        return $this->activated;
    }
    
    public function setActivated(bool $activated): self
    {
        $this->activated = $activated;
        
        return $this;
    }
    
    public function getFile()
    {
        return $this->file;
    }
    
    public function setFile(UploadedFile $file): self
    {
        $this->file = $file;
        
        return $this;
    }
    
    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }
    
    public function setImagePath(string $imagePath): self
    {
        $this->imagePath = $imagePath;
        
        return $this;
    }
    
    public function getImageName(): ?string
    {
        return $this->imageName;
    }
    
    public function setImageName(string $imageName): self
    {
        $this->imageName = $imageName;
        
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return serialize([$this->id, $this->username, $this->password]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        [$this->id, $this->username, $this->password] = unserialize($serialized, ['allowed_classes' => false]);
    }
    
    public function getResetToken(): ?string
    {
        return $this->reset_token;
    }
    
    public function setResetToken(?string $reset_token): self
    {
        $this->reset_token = $reset_token;
        
        return $this;
    }
    public function getToken(): ?string
    {
        return $this->token;
    }

    
    public function setToken(string $token): self
    {
        $this->token = $token;
    
        return $this;
    }
    
    public function __toString()
    {
        return $this->username;
    }
}
