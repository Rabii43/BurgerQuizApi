<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @property int countTh
 * @ORM\Entity(repositoryClass=CategorieRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Categorie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nameCategorie;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $photoCategorie;

    /**
     * @ORM\OneToMany(targetEntity=Theme::class, mappedBy="categorie", fetch="LAZY")
     */
    private $theme;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_modif;

private $categorie;
    public $countTh;



    public function __construct()
    {
        $this->theme = new ArrayCollection();

    }
    /**
     * @ORM\PostLoad()
     */
    public function countFn()
    {
        $this->countTh = count($this->theme);
    }
    public function getCountTh(): ?int
    {
        return $this->countTh;
    } public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameCategorie(): ?string
    {
        return $this->nameCategorie;
    }

    public function setNameCategorie(string $nameCategorie): self
    {
        $this->nameCategorie= $nameCategorie;

        return $this;
    }

    public function getPhotoCategorie(): ?string
    {
        return $this->photoCategorie;
    }

    public function setPhotoCategorie(string $photoCategorie): self
    {
        $this->photoCategorie = $photoCategorie;

        return $this;
    }

    /**
     * @return Collection|theme[]
     */
    public function getTheme(): Collection
    {
        return $this->theme;
    }

    public function addTheme(theme $theme): self
    {
        if (!$this->theme->contains($theme)) {
            $this->theme[] = $theme;
            $theme->setCategorie($this);
        }

        return $this;
    }

    public function removeTheme(theme $theme): self
    {
        if ($this->theme->removeElement($theme)) {
            // set the owning side to null (unless already changed)
            if ($theme->getCategorie() === $this) {
                $theme->setCategorie(null);
            }
        }

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getDateModif(): ?\DateTimeInterface
    {
        return $this->date_modif;
    }

    public function setDateModif(\DateTimeInterface $date_modif): self
    {
        $this->date_modif = $date_modif;

        return $this;
    }


}
