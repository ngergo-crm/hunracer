<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\ApiPlatform\UserIsMeFilter;
use App\Repository\UserRepository;
use App\Validator\CheckOldPasswordBeforeChange;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ApiResource(
 *     accessControl="is_granted('ROLE_USER')",
 *     collectionOperations={
 *          "get"={"access_control"="is_granted('ROLE_ADMIN')"},
 *          "post"={
 *              "access_control"="is_granted('ROLE_SUPER_ADMIN')",
 *              "validation_groups"={"Default", "create"}
 *          },
 *          "get_me"={
 *              "access_control"="is_granted('ROLE_USER')",
 *              "method"="GET",
 *              "pagination_enabled"=false,
 *              "path"="/users/me"
 *           }
 *     },
 *     itemOperations={
 *          "get",
 *          "put"={"access_control"="is_granted('ROLE_USER')"},
 *          "change_passord"={
 *               "access_control"="is_granted('ROLE_USER')",
 *               "method"="PUT",
 *               "validation_groups"={"Default", "changePass"},
 *               "path"="/users/{id}/changePassword"
 *          },
 *          "delete"={"access_control"="is_granted('ROLE_SUPER_ADMIN')"}
 *     }
 * )
 * @ApiFilter(PropertyFilter::class)
 * @ApiFilter(UserIsMeFilter::class)
 * @ApiFilter(BooleanFilter::class, properties={"isEnabled"})
 * @UniqueEntity(fields={"email"})
 * @UniqueEntity(fields={"uuid"})
 * @ORM\EntityListeners({"App\Doctrine\UserSetIsEnabledListener"})
 */
class User implements UserInterface, TimestampableInterface
{

    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @ApiProperty(identifier=false)
     */
    private $id;

    /**
     * @ORM\Column(type="uuid", unique=true)
     * @ApiProperty(identifier=true)
     * @Groups({"admin:write", "owner:read"})
     * @SerializedName("id")
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user:read", "admin:write"})
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups({"admin:read", "admin:write"})
     * @Assert\NotBlank()
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Groups({"admin:write", "user:write"})
     * @SerializedName("password")
     * @Assert\NotBlank(groups={"create", "changePass"})
     */
    private $plainPassword;

    /**
     * Used only for changing password on the account page
     * @Groups({"user:write"})
     * @SerializedName("current_password")
     * @Assert\NotBlank(groups={"changePass"})
     * @CheckOldPasswordBeforeChange(groups={"changePass"})
     */
    private $plainOldPassword;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"admin:write", "admin:read"})
     */
    private $isEnabled;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"user:read", "admin:write", "user:write"})
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Groups({"admin:read", "owner:read", "user:write"})
     */
    private $phone;

    /**
     * Returns true if this is the currently-authenticated user
     *
     * @SerializedName("isMe")
     * @Groups({"user:read", "owner:read"})
     */
    private $isMe = false;

    /**
     * @SerializedName("roleDescription")
     * @Groups({"user:read", "owner:read"})
     */
    private $roleDescription;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Groups({"admin:read", "owner:read", "user:write"})
     */
    private $trainerCode;

    /**
     * @ORM\OneToMany(targetEntity=Workouts::class, mappedBy="user", orphanRemoval=true)
     */
    private $workouts;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"admin:read", "owner:read", "user:write"})
     */
    private $birthday;

    /**
     * @ORM\ManyToMany(targetEntity=Section::class, inversedBy="users")
     * @Groups({"user:read", "user:write"})
     */
    private $sections;

//@Groups({"user:read","user:write", "admin:write"})
    /**
     * @ORM\ManyToOne(targetEntity=Team::class, inversedBy="users", cascade={"persist"})
     *
     */
    private $team;

    public function __construct(UuidInterface $uuid = null)
    {
        $this->uuid = $uuid ?: Uuid::uuid4();
        $this->workouts = new ArrayCollection();
        $this->sections = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        if (!$roles) {
            $roles[] = 'ROLE_USER';
        }
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getIsEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function setPlainOldPassword(string $plainOldPassword): self
    {
        $this->plainOldPassword = $plainOldPassword;
        return $this;
    }

    /**
     * @Groups({"user:read"})
     * Returns createdAt.
     * @SerializedName("createdAt")
     */
    // createdAT provided by TimestampableTrait.
    public function getCreatedAtTimestampable(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getIsMe(): bool
    {
        return $this->isMe;
    }

    public function setIsMe(bool $isMe)
    {
        $this->isMe = $isMe;
    }

    public function getRoleDescription(): ?string
    {
        $description = null;
        if (isset($this->roles[0])) {
            $role = $this->roles[0];
            if ($role === 'ROLE_USER') {
                $description = 'sportoló';
            } elseif ($role === 'ROLE_TRAINER') {
                $description = 'edző';
            } elseif ($role === 'ROLE_ADMIN') {
                $description = 'admin';
            } else {
                $description = 'szuperAdmin';
            }
        }
        return $description;
    }

    public function setTrainerCode(string $trainerCode = ''): self
    {
        $this->trainerCode = $trainerCode;
        return $this;
    }

    public function getTrainerCode(): string
    {
        if($this->trainerCode === null) {
            $this->trainerCode = '';
        }
        return $this->trainerCode;
    }

    /**
     * @return Collection|Workouts[]
     */
    public
    function getWorkouts(): Collection
    {
        return $this->workouts;
    }

    public
    function addWorkout(Workouts $workout): self
    {
        if (!$this->workouts->contains($workout)) {
            $this->workouts[] = $workout;
            $workout->setUser($this);
        }

        return $this;
    }

    public
    function removeWorkout(Workouts $workout): self
    {
        if ($this->workouts->removeElement($workout)) {
            // set the owning side to null (unless already changed)
            if ($workout->getUser() === $this) {
                $workout->setUser(null);
            }
        }

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * @return Collection|Section[]
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(Section $section): self
    {
        if (!$this->sections->contains($section)) {
            $this->sections[] = $section;
        }

        return $this;
    }

    public function removeSection(Section $section): self
    {
        $this->sections->removeElement($section);

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }
}
