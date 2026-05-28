<?php

namespace App\Entity;

use AllowDynamicProperties;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[AllowDynamicProperties]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;


    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private bool $isVerified = false;

    /**
     * @var Collection<int, Channel>
     */
    #[ORM\OneToMany(targetEntity: Channel::class, mappedBy: 'creator', orphanRemoval: true)]
    private Collection $createdChannels;

    /**
     * @var Collection<int, ChannelUser>
     */
    #[ORM\OneToMany(targetEntity: ChannelUser::class, mappedBy: 'member', orphanRemoval: true)]
    private Collection $joinedChannels;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'user_id', orphanRemoval: true)]
    private Collection $messages;

    #[ORM\Column(length: 15)]
    private ?string $media_type = null;

    #[ORM\Column]
    private ?int $progression_number = null;

    public function __construct()
    {
        $this->createdChannels = new ArrayCollection();
        $this->joinedChannels = new ArrayCollection();
        $this->messages = new ArrayCollection();

        $this->mediaType = 'pending';
        $this->progressionNumber = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->loginIdentifier ?? (string)$this->username;
    }

    public function setLoginIdentifier(string $identifier): self
    {
        $this->loginIdentifier = $identifier;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Channel>
     */
    public function getCreatedChannels(): Collection
    {
        return $this->createdChannels;
    }

    public function addCreatedChannel(Channel $createdChannel): static
    {
        if (!$this->createdChannels->contains($createdChannel)) {
            $this->createdChannels->add($createdChannel);
            $createdChannel->setCreator($this);
        }

        return $this;
    }

    public function removeCreatedChannel(Channel $createdChannel): static
    {
        if ($this->createdChannels->removeElement($createdChannel)) {
            // set the owning side to null (unless already changed)
            if ($createdChannel->getCreator() === $this) {
                $createdChannel->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ChannelUser>
     */
    public function getJoinedChannels(): Collection
    {
        return $this->joinedChannels;
    }

    public function addJoinedChannel(ChannelUser $joinedChannel): static
    {
        if (!$this->joinedChannels->contains($joinedChannel)) {
            $this->joinedChannels->add($joinedChannel);
            $joinedChannel->setMember($this);
        }

        return $this;
    }

    public function removeJoinedChannel(ChannelUser $joinedChannel): static
    {
        if ($this->joinedChannels->removeElement($joinedChannel)) {
            // set the owning side to null (unless already changed)
            if ($joinedChannel->getMember() === $this) {
                $joinedChannel->setMember(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setUserId($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getUserId() === $this) {
                $message->setUserId(null);
            }
        }

        return $this;
    }

    public function getMediaType(): ?string
    {
        return $this->media_type;
    }

    public function setMediaType(string $media_type): static
    {
        $this->media_type = $media_type;

        return $this;
    }

    public function getProgressionNumber(): ?int
    {
        return $this->progression_number;
    }

    public function setProgressionNumber(int $progression_number): static
    {
        $this->progression_number = $progression_number;

        return $this;
    }
}
