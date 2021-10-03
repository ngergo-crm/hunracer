<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\WorkoutDetailRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     accessControl="is_granted('IS_AUTHENTICATED_FULLY')"
 * )
 * @ORM\Entity(repositoryClass=WorkoutDetailRepository::class)
 */
class WorkoutDetail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Workouts::class, inversedBy="workoutDetail", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $workout;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $data = [];

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $updatedAt;

    public function __construct()
    {
        $this->setUpdatedAt(new \DateTime('now'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWorkout(): ?Workouts
    {
        return $this->workout;
    }

    public function setWorkout(Workouts $workout): self
    {
        $this->workout = $workout;

        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
