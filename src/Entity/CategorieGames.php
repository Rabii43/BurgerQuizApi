<?php

namespace App\Entity;

use App\Repository\CategorieGamesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @property ArrayCollection categorieJeu
 * @ORM\Entity(repositoryClass=CategorieGamesRepository::class)
 */
class CategorieGames
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=Question::class, mappedBy="categorieGames")
     */
    private $collectQuestion;

    /**
     * @ORM\ManyToOne(targetEntity=Games::class, inversedBy="CategorieGame")
     */
    private $games;



    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $questionsList;

    public function __construct()
    {
        $this->categorieJeu = new ArrayCollection();
        $this->collectQuestion = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|question[]
     */
    public function getCollectQuestion(): Collection
    {
        return $this->collectQuestion;
    }

    public function addCollectQuestion(question $collectQuestion): self
    {
        if (!$this->collectQuestion->contains($collectQuestion)) {
            $this->collectQuestion[] = $collectQuestion;
            $collectQuestion->setCategorieGames($this);
        }

        return $this;
    }

    public function removeCollectQuestion(question $collectQuestion): self
    {
        if ($this->collectQuestion->removeElement($collectQuestion)) {
            // set the owning side to null (unless already changed)
            if ($collectQuestion->getCategorieGames() === $this) {
                $collectQuestion->setCategorieGames(null);
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

    public function getQuestionsList(): ?string
    {
        return $this->questionsList;
    }

    public function setQuestionsList(?string $questionsList): self
    {
        $this->questionsList = $questionsList;

        return $this;
    }
}
