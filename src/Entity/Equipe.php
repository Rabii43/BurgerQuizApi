<?php

namespace App\Entity;

use App\Repository\EquipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EquipeRepository::class)
 */
class Equipe
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
    private $responsable;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nameEq;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="equipe")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Games::class, inversedBy="equipe")
     */
    private $games;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $useres;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $scort;


    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getNameEq(): ?string
    {
        return $this->nameEq;
    }

    public function setNameEq(string $nameEq): self
    {
        $this->nameEq = $nameEq;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getResponsable(): ?string
    {
        return $this->responsable;
    }

    public function setResponsable(string $responsable): self
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * @return Collection|user[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(user $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
            $user->setEquipe($this);
        }

        return $this;
    }

    public function removeUser(user $user): self
    {
        if ($this->user->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getEquipe() === $this) {
                $user->setEquipe(null);
            }
        }

        return $this;
    }

    public function getGames(): ?Games
    {
        return $this->games;
    }

    public function setGames(?Games $games): self
    {
        $this->games = $games;

        return $this;
    }

    public function getUseres(): ?string
    {
        return $this->useres;
    }

    public function setUseres(?string $useres): self
    {
        $this->useres = $useres;

        return $this;
    }

    public function getScort(): ?int
    {
        return $this->scort;
    }

    public function setScort(?int $scort): self
    {
        $this->scort = $scort;

        return $this;
    }


}
