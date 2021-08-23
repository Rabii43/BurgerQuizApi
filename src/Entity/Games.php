<?php

namespace App\Entity;

use App\Repository\GamesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @property ArrayCollection categorie
 * @ORM\Entity(repositoryClass=GamesRepository::class)
 */
class Games
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $animateur;
    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $date_creation;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $date_jeu;


    /**
     * @ORM\OneToMany(targetEntity=CategorieGames::class, mappedBy="games")
     */
    private $categorieGame;

    /**
     * @ORM\OneToMany(targetEntity=Equipe::class, mappedBy="games")
     */
    private $equipe;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $corantQuestion;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $corantList;

    public function __construct()
    {
        $this->equipe = new ArrayCollection();
        $this->categorie = new ArrayCollection();
        $this->categorieGame = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getAnimateur(): ?string
    {
        return $this->animateur;
    }
    public function setAnimateur(string $animateur): self
    {
        $this->animateur = $animateur;

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

    public function getDateJue(): ?\DateTimeInterface
    {
        return $this->date_jeu;
    }

    public function setDateJeu(\DateTimeInterface $date_jeu): self
    {
        $this->date_jeu = $date_jeu;

        return $this;
    }
    /**
     * @return Collection|CategorieGames[]
     */
    public function getCategorieGame(): Collection
    {
        return $this->categorieGame;
    }

    public function addCategorieGame(CategorieGames $categorieGame): self
    {
        if (!$this->categorieGame->contains($categorieGame)) {
            $this->categorieGame[] = $categorieGame;
            $categorieGame->setGames($this);
        }

        return $this;
    }

    public function removeCategorieGame(CategorieGames $categorieGame): self
    {
        if ($this->categorieGame->removeElement($categorieGame)) {
            // set the owning side to null (unless already changed)
            if ($categorieGame->getGames() === $this) {
                $categorieGame->setGames(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Equipe[]
     */
    public function getEquipe(): Collection
    {
        return $this->equipe;
    }

    public function addEquipe(Equipe $equipe): self
    {
        if (!$this->equipe->contains($equipe)) {
            $this->equipe[] = $equipe;
            $equipe->setGames($this);
        }

        return $this;
    }

    public function removeEquipe(Equipe $equipe): self
    {
        if ($this->equipe->removeElement($equipe)) {
            // set the owning side to null (unless already changed)
            if ($equipe->getGames() === $this) {
                $equipe->setGames(null);
            }
        }

        return $this;
    }

    public function getCorantQuestion(): ?string
    {
        return $this->corantQuestion;
    }

    public function setCorantQuestion(?string $corantQuestion): self
    {
        $this->corantQuestion = $corantQuestion;

        return $this;
    }

    public function getCorantList(): ?string
    {
        return $this->corantList;
    }

    public function setCorantList(?string $corantList): self
    {
        $this->corantList = $corantList;

        return $this;
    }


}
