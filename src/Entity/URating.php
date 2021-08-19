<?php

namespace App\Entity;

use App\Repository\URatingRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=URatingRepository::class)
 * @ApiResource(
 *     accessControl="is_granted('ROLE_USER')",
 *     collectionOperations={
 *          "get"={
 *     }
 *     },
 *     itemOperations={
 *          "get"={}
 *     }
 * )
 */
class URating
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json")
     * @Groups({"urating:read", "user:read", "admin:read"})
     */
    private $ageInterval = [];

    /**
     * @ORM\Column(type="string", length=10)
     * @Groups({"urating:read", "user:read","admin:read"})
     */
    private $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAgeInterval(): ?array
    {
        return $this->ageInterval;
    }

    public function setAgeInterval(array $ageInterval): self
    {
        $this->ageInterval = $ageInterval;

        return $this;
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
}
