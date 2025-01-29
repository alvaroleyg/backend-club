<?php

namespace App\Entity;

use App\Repository\ClubRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClubRepository::class)]
class Club
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "El nombre no puede estar vacío.")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "El nombre debe tener al menos {{ limit }} caracteres",
        maxMessage: "El nombre no puede tener más de {{ limit }} caracteres"
    )]
    private string $name;

    #[ORM\Column(type: "float", nullable: true)]
    #[Assert\NotBlank(message: "El presupuesto no puede estar vacío")]
    #[Assert\Positive(message: "El presupuesto debe ser un número positivo")]
    private ?float $budget = null;

    #[ORM\OneToMany(targetEntity: Player::class, mappedBy: "club")]
    private Collection $players;

    #[ORM\OneToMany(targetEntity: Coach::class, mappedBy: "club")]
    private Collection $coaches;

    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->coaches = new ArrayCollection();
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

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(float $budget): self
    {
        $this->budget = $budget;
        return $this;
    }
    
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players[] = $player;
            $player->setClub($this);
        }
        return $this;
    }

    public function removePlayer(Player $player): self
    {
        if ($this->players->removeElement($player)) {
            if ($player->getClub() === $this) {
                $player->setClub(null);
            }
        }
        return $this;
    }

    public function getCoaches(): Collection
    {
        return $this->coaches;
    }

    public function addCoach(Coach $coach): self
    {
        if (!$this->coaches->contains($coach)) {
            $this->coaches[] = $coach;
            $coach->setClub($this);
        }
        return $this;
    }

    public function removeCoach(Coach $coach): self
    {
        if ($this->coaches->removeElement($coach)) {
            if ($coach->getClub() === $this) {
                $coach->setClub(null);
            }
        }
        return $this;
    }

    public function calculateTotalSalaries(): float
    {
        $total = 0;

        foreach ($this->players as $player) {
            $total += $player->getSalary() ?? 0;
        }

        foreach ($this->coaches as $coach) {
            $total += $coach->getSalary() ?? 0;
        }

        return $total;
    }
}
