<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\MetricRecordRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;

/**
 * @ApiResource(
 *     accessControl="is_granted('IS_AUTHENTICATED_FULLY')",
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_USER')",
 *              "normalization_context"={"groups"={"metricrecord:read"}},
 *              "denormalizationContext"={"groups"={"metricrecord:write"}},
 *              "pagination_enabled"=false
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_TRAINER')",
 *              "validation_groups"={"Default", "create"}
 *          }
 *     }
 * )
 * @ApiFilter(SearchFilter::class, properties={
 *     "user":"exact",
 *     "user.uuid":"exact",
 *     "metricCreatedAt":"exact"
 * })
 * @ORM\Entity(repositoryClass=MetricRecordRepository::class)
 */
class MetricRecord implements TimestampableInterface
{

    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"metricrecord:read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="metricRecords")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"metricrecord:write"})
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"metricrecord:read", "metricrecord:write", "user:read"})
     */
    private $metricCreatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=MetricType::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"metricrecord:read", "metricrecord:write", "user:read"})
     */
    private $axisXType;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Groups({"metricrecord:read", "metricrecord:write", "user:read"})
     */
    private $data;

    /**
     * @ORM\ManyToOne(targetEntity=MetricType::class)
     * @Groups({"metricrecord:read", "metricrecord:write", "user:read"})
     */
    private $axisYType;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMetricCreatedAt(): ?\DateTimeInterface
    {
        return $this->metricCreatedAt;
    }

    public function setMetricCreatedAt(\DateTimeInterface $metricCreatedAt): self
    {
        $this->metricCreatedAt = $metricCreatedAt;

        return $this;
    }

    public function getAxisXType(): ?MetricType
    {
        return $this->axisXType;
    }

    public function setAxisXType(?MetricType $axisXType): self
    {
        $this->axisXType = $axisXType;

        return $this;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(string $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getAxisYType(): ?MetricType
    {
        return $this->axisYType;
    }

    public function setAxisYType(?MetricType $axisYType): self
    {
        $this->axisYType = $axisYType;

        return $this;
    }

    /**
     * @Groups({"user:read"})
     * Returns createdAt.
     * @SerializedName("recordCreatedAt")
     */
    // createdAT provided by TimestampableTrait.
    public function getCreatedAtTimestampable(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }
}
