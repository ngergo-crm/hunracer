<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\WorkoutsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ApiResource(
 *     accessControl="is_granted('IS_AUTHENTICATED_FULLY')",
 *     collectionOperations={
 *          "get"={
 *              "normaliyation_context"={"groups"={"workouts:read", "user:item:read"}},
 *              "pagination_enabled"=false,
 *          },
 *          "getWorkoutDays"={
 *              "access_control"="is_granted('ROLE_USER')",
 *              "method"="GET",
 *              "pagination_enabled"=false,
 *              "path"="/workouts/workoutdays"
 *           }
 *     },
 *     itemOperations={
 *          "get"
 *     }
 * )
 * @UniqueEntity(fields={"id"})
 * @ApiFilter(DateFilter::class, properties={"workoutDay"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "user":"exact",
 *     "user.uuid":"exact"
 * })
 * @ORM\Entity(repositoryClass=WorkoutsRepository::class)
 */
class Workouts
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @ApiProperty(identifier=true)
     */
    private $id;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $data = [];

    /**
     * @ORM\Column(type="bigint")
     * @Groups({"workouts:read"})
     */
    private $workoutId;

    /**
     * @ORM\Column(type="date")
     * @Groups({"workouts:read"})
     */
    private $workoutDay;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"workouts:read"})
     */
    private $distance;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"workouts:read"})
     */
    private $totalTime;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"workouts:read"})
     */
    private $energy;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"workouts:read"})
     */
    private $tss;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"workouts:read"})
     */
    private $elevation;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="workouts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Groups({"workouts:read"})
     */
    private $type;

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

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getWorkoutId(): ?string
    {
        return $this->workoutId;
    }

    public function setWorkoutId(string $workoutId): self
    {
        $this->workoutId = $workoutId;

        return $this;
    }

    public function getWorkoutDay(): ?\DateTimeInterface
    {
        return $this->workoutDay;
    }

    public function setWorkoutDay(\DateTimeInterface $workoutDay): self
    {
        $this->workoutDay = $workoutDay;

        return $this;
    }

    public function getDistance(): ?float
    {
        return $this->distance;
    }

    public function setDistance(?float $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function getTotalTime(): ?float
    {
        return $this->totalTime;
    }

    public function setTotalTime(?float $totalTime): self
    {
        $this->totalTime = $totalTime;

        return $this;
    }

    public function getEnergy(): ?float
    {
        return $this->energy;
    }

    public function setEnergy(?float $energy): self
    {
        $this->energy = $energy;

        return $this;
    }

    public function getTss(): ?float
    {
        return $this->tss;
    }

    public function setTss(?float $tss): self
    {
        $this->tss = $tss;

        return $this;
    }

    public function getElevation(): ?float
    {
        return $this->elevation;
    }

    public function setElevation(?float $elevation): self
    {
        $this->elevation = $elevation;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

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
