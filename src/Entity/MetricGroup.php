<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\MetricGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     accessControl="is_granted('IS_AUTHENTICATED_FULLY')",
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_USER')",
 *              "normalization_context"={"groups"={"metricgroup:read", "metrictypes:item:read"}},
 *              "denormalizationContext"={"groups"={"metricgroup:write"}},
 *              "pagination_enabled"=false
 *     },
 *          "post"={
 *              "access_control"="is_granted('ROLE_TRAINER')",
 *              "validation_groups"={"Default", "create"}
 *          }
 *     }
 * )
 * @ORM\Entity(repositoryClass=MetricGroupRepository::class)
 */
class MetricGroup
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"metrictype:read", "metricgroup:read", "metricgroup:write"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"metrictype:read", "metricgroup:read", "metricgroup:write"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=MetricType::class, mappedBy="metricGroup")
     */
    private $metricTypes;

    public function __construct()
    {
        $this->metricTypes = new ArrayCollection();
    }

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

    /**
     * @return Collection|MetricType[]
     */
    public function getMetricTypes(): Collection
    {
        return $this->metricTypes;
    }

    public function addMetricType(MetricType $metricType): self
    {
        if (!$this->metricTypes->contains($metricType)) {
            $this->metricTypes[] = $metricType;
            $metricType->setMetricGroup($this);
        }

        return $this;
    }

    public function removeMetricType(MetricType $metricType): self
    {
        if ($this->metricTypes->removeElement($metricType)) {
            // set the owning side to null (unless already changed)
            if ($metricType->getMetricGroup() === $this) {
                $metricType->setMetricGroup(null);
            }
        }

        return $this;
    }
}
