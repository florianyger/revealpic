<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PieceRepository")
 */
class Piece
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Page", inversedBy="pieces")
     * @ORM\JoinColumn(nullable=false)
     */
    private $page;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $filename;

    /**
     * @ORM\Column(type="integer")
     */
    private $width;

    /**
     * @ORM\Column(type="integer")
     */
    private $height;

    /**
     * @ORM\Column(type="integer")
     */
    private $leftPos;

    /**
     * @ORM\Column(type="integer")
     */
    private $topPos;

    /**
     * @ORM\Column(type="boolean")
     */
    private $revealed = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbClickToReveal = 15;

    public function getId()
    {
        return $this->id;
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function setFilename($filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getLeftPos(): ?int
    {
        return $this->leftPos;
    }

    public function setLeftPos(int $leftPos): self
    {
        $this->leftPos = $leftPos;

        return $this;
    }

    public function getTopPos(): ?int
    {
        return $this->topPos;
    }

    public function setTopPos(int $topPos): self
    {
        $this->topPos = $topPos;

        return $this;
    }

    public function getRevealed(): ?bool
    {
        return $this->revealed;
    }

    public function setRevealed(bool $revealed): self
    {
        $this->revealed = $revealed;

        return $this;
    }

    public function getNbClickToReveal(): ?int
    {
        return $this->nbClickToReveal;
    }

    public function setNbClickToReveal(?int $nbClickToReveal): self
    {
        $this->nbClickToReveal = $nbClickToReveal;

        return $this;
    }

    public function addClickToReveal()
    {
        if ($this->revealed) {
            return;
        }

        $this->nbClickToReveal--;

        if (0 === $this->nbClickToReveal) {
            $this->revealed = true;
        }
    }
}
