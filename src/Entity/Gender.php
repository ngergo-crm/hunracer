<?php

namespace App\Entity;

use App\Repository\GenderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     accessControl="is_granted('ROLE_USER')",
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_USER')",
 *              "normalizationContext"={"groups"={"gender:read"}},
 *              "denormalizationContext"={"groups"={"gender:write"}},
 *      },
 *          "post"={
 *              "access_control"="is_granted('ROLE_ADMIN')",
 *              "validation_groups"={"Default", "create"}
 *          }
 *     }
 * )
 * @ORM\Entity(repositoryClass=GenderRepository::class)
 */
class Gender
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"user:read", "gender:collection:get", "admin:read", "admin:write"})
     * @ORM\Column(type="string", length=20)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="gender")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=Performance::class, mappedBy="gender")
     */
    private $performance;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->performance = new ArrayCollection();
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

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setGender($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getGender() === $this) {
                $user->setGender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Performance[]
     */
    public function getPerformance(): Collection
    {
        return $this->performance;
    }
}
