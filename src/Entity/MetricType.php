<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\MetricTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     accessControl="is_granted('IS_AUTHENTICATED_FULLY')",
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_USER')",
 *              "normalization_context"={"groups"={"metrictype:read"}},
 *              "denormalizationContext"={"groups"={"metrictype:write"}},
 *              "pagination_enabled"=false},
 *          "post"={
 *              "access_control"="is_granted('ROLE_TRAINER')",
 *              "validation_groups"={"Default", "create"}
 *          }
 *     }
 * )
 * @ORM\Entity(repositoryClass=MetricTypeRepository::class)
 */
class MetricType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"metrictype:write", "metrictype:read"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"metrictype:write", "metrictype:read", "metricrecord:read"})
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=MetricGroup::class, inversedBy="metricTypes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"metrictype:write", "metrictype:read"})
     */
    private $metricGroup;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     * @Groups({"metrictype:write", "metrictype:read"})
     */
    private $axis;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Groups({"metrictype:write", "metrictype:read"})
     */
    private $unit;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
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

    public function getMetricGroup(): ?MetricGroup
    {
        return $this->metricGroup;
    }

    public function setMetricGroup(?MetricGroup $metricGroup): self
    {
        $this->metricGroup = $metricGroup;

        return $this;
    }

    public function getAxis(): ?string
    {
        return $this->axis;
    }

    public function setAxis(?string $axis): self
    {
        $this->axis = $axis;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }
}
