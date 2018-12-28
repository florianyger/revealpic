<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PageRepository")
 * @Vich\Uploadable
 */
class Page
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $slug;

    /**
     * @Assert\Image(
     *     minWidth = 400,
     *     minHeight = 200
     * )
     *
     * @Vich\UploadableField(mapping="page_picture", fileNameProperty="pictureName", size="pictureSize", dimensions="pictureDimensions")
     *
     * @var File
     */
    private $pictureFile;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $pictureName;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $pictureSize;

    /**
     * @ORM\Column(type="json")
     *
     * @var array
     */
    private $pictureDimensions;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $viewCount = 0;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Piece", mappedBy="page", orphanRemoval=true)
     */
    private $pieces;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->slug = $this->generateSlug();
        $this->updatedAt = new \DateTime();
        $this->pieces = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return Page
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return File|UploadedFile|null
     */
    public function getPictureFile(): ?File
    {
        return $this->pictureFile;
    }

    /**
     * @param File|UploadedFile $picture
     *
     * @throws \Exception
     */
    public function setPictureFile(?File $picture = null): void
    {
        $this->pictureFile = $picture;

        if (null !== $picture) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    /**
     * @return string|null
     */
    public function getPictureName(): ?string
    {
        return $this->pictureName;
    }

    /**
     * @param string|null $pictureName
     */
    public function setPictureName(?string $pictureName): void
    {
        $this->pictureName = $pictureName;
    }

    /**
     * @return int|null
     */
    public function getPictureSize(): ?int
    {
        return $this->pictureSize;
    }

    /**
     * @param int|null $pictureSize
     */
    public function setPictureSize(?int $pictureSize): void
    {
        $this->pictureSize = $pictureSize;
    }

    /**
     * @return array
     */
    public function getPictureDimensions(): array
    {
        return $this->pictureDimensions;
    }

    /**
     * @param array $pictureDimensions
     */
    public function setPictureDimensions(array $pictureDimensions): void
    {
        $this->pictureDimensions = $pictureDimensions;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return int|null
     */
    public function getViewCount(): ?int
    {
        return $this->viewCount;
    }

    /**
     * @param int $viewCount
     *
     * @return Page
     */
    public function setViewCount(int $viewCount): self
    {
        $this->viewCount = $viewCount;

        return $this;
    }

    /**
     * @return Page
     */
    public function addViewCount(): self
    {
        ++$this->viewCount;

        return $this;
    }

    /**
     * @return Collection|Piece[]
     */
    public function getPieces(): Collection
    {
        return $this->pieces;
    }

    /**
     * @param Piece $piece
     *
     * @return Page
     */
    public function addPiece(Piece $piece): self
    {
        if (!$this->pieces->contains($piece)) {
            $this->pieces[] = $piece;
            $piece->setPage($this);
        }

        return $this;
    }

    /**
     * @param Piece $piece
     *
     * @return Page
     */
    public function removePiece(Piece $piece): self
    {
        if ($this->pieces->contains($piece)) {
            $this->pieces->removeElement($piece);
            // set the owning side to null (unless already changed)
            if ($piece->getPage() === $this) {
                $piece->setPage(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function __toString()
    {
        return $this->getSlug();
    }

    /**
     * @return bool|string
     */
    private function generateSlug()
    {
        return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 10);
    }
}
