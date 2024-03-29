<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     accessControl="is_granted('ROLE_USER')",
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_USER')",
 *              "normalizationContext"={"groups"={"team:read"}},
 *              "pagination_enabled"=false
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_ADMIN')",
 *              "validation_groups"={"Default", "create"}
 *          }
 *     }
 * )
 * @ORM\Entity(repositoryClass=TeamRepository::class)
 */
class Team
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read", "section:collection:get", "admin:read", "admin:write"})
     */
    private $fullname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read", "team:collection:get", "admin:read", "admin:write"})
     */
    private $shortname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"admin:read", "admin:write"})
     */
    private $contactname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"admin:read", "admin:write"})
     */
    private $contactmail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"admin:read", "admin:write"})
     */
    private $contactphone;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $updatedAt;

    //itt team volt a mappedBy
    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="user")
     */
    private $users;

    public function __construct()
    {
        $this->setUpdatedAt(new \DateTime('now'));
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getShortname(): ?string
    {
        return $this->shortname;
    }

    public function setShortname(string $shortname): self
    {
        $this->shortname = $shortname;

        return $this;
    }

    public function getContactname(): ?string
    {
        return $this->contactname;
    }

    public function setContactname(?string $contactname): self
    {
        $this->contactname = $contactname;

        return $this;
    }

    public function getContactmail(): ?string
    {
        return $this->contactmail;
    }

    public function setContactmail(?string $contactmail): self
    {
        $this->contactmail = $contactmail;

        return $this;
    }

    public function getContactphone(): ?string
    {
        return $this->contactphone;
    }

    public function setContactphone(?string $contactphone): self
    {
        $this->contactphone = $contactphone;

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
            $user->setTeam($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getTeam() === $this) {
                $user->setTeam(null);
            }
        }

        return $this;
    }
}
