<?php

namespace App\Entity;

use App\Repository\PerformanceRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *  @ORM\Entity(repositoryClass=PerformanceRepository::class)
 *  @ApiResource(
 *     accessControl="is_granted('ROLE_ADMIN')",
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_ADMIN')",
 *              "normalizationContext"={"groups"={"performance:read"}},
 *              "denormalizationContext"={"groups"={"performance:write"}},
 *              "pagination_enabled"=false
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_ADMIN')",
 *              "validation_groups"={"Default", "create"}
 *          }
 *     },
 *     itemOperations={
 *          "get"={},
 *          "put"={}
 *     }
 * )
 */
class Performance
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Gender::class, inversedBy="performance")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"performance:read", "performance:write"})
     */
    private $gender;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"performance:read", "performance:write"})
     */
    private $workoutTimePerYear;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"performance:read", "performance:write"})
     */
    private $workoutDistancePerYear;

    /**
     * @ORM\ManyToOne(targetEntity=URating::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"performance:read", "performance:write"})
     */
    private $uRating;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    public function setGender(?Gender $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getWorkoutTimePerYear(): ?int
    {
        return $this->workoutTimePerYear;
    }

    public function setWorkoutTimePerYear(int $workoutTimePerYear): self
    {
        $this->workoutTimePerYear = $workoutTimePerYear;

        return $this;
    }

    public function getWorkoutDistancePerYear(): ?int
    {
        return $this->workoutDistancePerYear;
    }

    public function setWorkoutDistancePerYear(int $workoutDistancePerYear): self
    {
        $this->workoutDistancePerYear = $workoutDistancePerYear;

        return $this;
    }

    public function getURating(): ?URating
    {
        return $this->uRating;
    }

    public function setURating(?URating $uRating): self
    {
        $this->uRating = $uRating;

        return $this;
    }
}
