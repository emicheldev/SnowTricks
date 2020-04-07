<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * @UniqueEntity("name")
 */
class Category
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
	 * @ORM\ManyToMany(targetEntity="App\Entity\Figure", mappedBy="categories")
	 */
	private $figure;

	public function __construct()
	{
		$this->figure = new ArrayCollection();
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

	/**
	 * @return Collection|Figure[]
	 */
	public function getFigure(): Collection
	{
		return $this->figure;
	}

	public function addFigure(Figure $figure): self
	{
		if (!$this->figure->contains($figure)) {
			$this->figure[] = $figure;
		}

		return $this;
	}

	public function removeFigure(Figure $figure): self
	{
		if ($this->figure->contains($figure)) {
			$this->figure->removeElement($figure);
		}

		return $this;
	}
}