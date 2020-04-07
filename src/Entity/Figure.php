<?php

namespace App\Entity;

use App\Entity\Video;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Entity\Category;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FigureRepository")
 * @UniqueEntity("name")
 * @Vich\Uploadable()
 */
class Figure
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $name;

	/**
	 * @ORM\Column(type="text")
	 */
	private $description;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $created_at;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $updated_at;

	/**
	 * @Groups("comments")
	 * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="figure")
	 */
	private $comments;

	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="figure")
	 */
	private $categories;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Picture", mappedBy="figure", orphanRemoval=true, cascade={"persist"})
	 */
	private $pictures;

	/**
	 * @Assert\All({
	 *   @Assert\Image(mimeTypes={"image/png", "image/jpeg"})
	 * })
	 */
	private $pictureFiles;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $main_image;

	/**
	 * @var File|null
	 * @Assert\Image(
	 *     mimeTypes={"image/png", "image/jpeg"}
	 * )
	 * @Vich\UploadableField(mapping="figure_main_image", fileNameProperty="main_image")
	 */
	private $mainImgFile;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Video", mappedBy="figure", cascade={"remove"})
	 */
	private $videos;

	public function __construct()
	{
		$this->comments = new ArrayCollection();
		$this->categories = new ArrayCollection();
		$this->pictures = new ArrayCollection();
		$this->videos = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->name;
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getSlug(): string
	{
		return (new Slugify())->slugify($this->name);
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(string $description): self
	{
		$this->description = $description;

		return $this;
	}

	public function getCreatedAt(): ?\DateTimeInterface
	{
		return $this->created_at;
	}

	public function setCreatedAt(\DateTimeInterface $created_at): self
	{
		$this->created_at = $created_at;

		return $this;
	}

	public function getUpdatedAt(): ?\DateTimeInterface
	{
		return $this->updated_at;
	}

	public function setUpdatedAt(\DateTimeInterface $updated_at): self
	{
		$this->updated_at = $updated_at;

		return $this;
	}

	public function dateIsSame()
	{
		return $this->created_at == $this->updated_at;
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
			$comment->setFigure($this);
		}

		return $this;
	}

	public function removeComment(Comment $comment): self
	{
		if ($this->comments->contains($comment)) {
			$this->comments->removeElement($comment);
			// set the owning side to null (unless already changed)
			if ($comment->getFigure() === $this) {
				$comment->setFigure(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection|Category[]
	 */
	public function getCategories(): Collection
	{
		return $this->categories;
	}

	public function addCategory(Category $category): self
	{
		if (!$this->categories->contains($category)) {
			$this->categories[] = $category;
			$category->addFigure($this);
		}

		return $this;
	}

	public function removeCategory(Category $category): self
	{
		if ($this->categories->contains($category)) {
			$this->categories->removeElement($category);
			$category->removeFigure($this);
		}

		return $this;
	}

	/**
	 * @return Collection|Picture[]
	 */
	public function getPictures(): Collection
	{
		return $this->pictures;
	}

	public function getPicture(): ?Picture
	{
		if ($this->pictures->isEmpty()) {
			return null;
		}
		return $this->pictures->first();
	}

	public function addPicture(Picture $picture): self
	{
		if (!$this->pictures->contains($picture)) {
			$this->pictures[] = $picture;
			$picture->setFigure($this);
		}

		return $this;
	}

	public function removePicture(Picture $picture): self
	{
		if ($this->pictures->contains($picture)) {
			$this->pictures->removeElement($picture);
			// set the owning side to null (unless already changed)
			if ($picture->getFigure() === $this) {
				$picture->setFigure(null);
			}
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPictureFiles()
	{
		return $this->pictureFiles;
	}

	/**
	 * @param mixed $pictureFiles
	 * @return Figure
	 */
	public function setPictureFiles($pictureFiles): self
	{
		foreach ($pictureFiles as $pictureFile) {
			$picture = new Picture();
			$picture->setImageFile($pictureFile);
			$this->addPicture($picture);
		}
		$this->pictureFiles = $pictureFiles;
		return $this;
	}

	public function getMainImage(): ?string
	{
		return $this->main_image;
	}

	public function setMainImage(?string $main_image): self
	{
		$this->main_image = $main_image;

		return $this;
	}

	/**
	 * Get mimeTypes={"image/png", "image/jpeg"}
	 *
	 * @return  File|null
	 */
	public function getMainImgFile()
	{
		return $this->mainImgFile;
	}

	/**
	 * Set mimeTypes={"image/png", "image/jpeg"}
	 *
	 * @param  File|null  $mainImgFile  mimeTypes={"image/png", "image/jpeg"}
	 *
	 * @return  self
	 */
	public function setMainImgFile($mainImgFile)
	{
		$this->mainImgFile = $mainImgFile;

		return $this;
	}

	/**
	 * @return Collection|Video[]
	 */
	public function getVideos(): Collection
	{
		return $this->videos;
	}

	public function addVideo(Video $video): self
	{
		if (!$this->videos->contains($video)) {
			$this->videos[] = $video;
			$video->setFigure($this);
		}

		return $this;
	}

	public function removeVideo(Video $video): self
	{
		if ($this->videos->contains($video)) {
			$this->videos->removeElement($video);
			// set the owning side to null (unless already changed)
			if ($video->getFigure() === $this) {
				$video->setFigure(null);
			}
		}

		return $this;
	}
}