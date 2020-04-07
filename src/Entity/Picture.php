<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PictureRepository")
 * @Vich\Uploadable()
 */
class Picture
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @var File|null
	 * @Assert\Image(
	 *     mimeTypes={"image/png", "image/jpeg"}
	 * )
	 * @Vich\UploadableField(mapping="figure_image", fileNameProperty="filename")
	 */
	private $imageFile;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $filename;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Figure", inversedBy="pictures")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $figure;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

	public function getId(): ?int
         	{
         		return $this->id;
         	}

	public function setId(?int $id): self
         	{
         		$this->id = $id;
         
         		return $this;
         	}

	public function getFilename(): ?string
         	{
         		return $this->filename;
         	}

	public function setFilename(?string $filename): self
         	{
         		$this->filename = $filename;
         
         		return $this;
         	}

	public function getFigure(): ?Figure
         	{
         		return $this->figure;
         	}

	public function setFigure(?Figure $figure): self
         	{
         		$this->figure = $figure;
         
         		return $this;
         	}

	/**
	 * @return null|File
	 */
	public function getImageFile(): ?File
         	{
         		return $this->imageFile;
         	}

	/**
	 * @param null|File $imageFile
	 * @return self
	 */
	public function setImageFile(?File $imageFile): self
         	{
         		$this->imageFile = $imageFile;
         		return $this;
         	}

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}