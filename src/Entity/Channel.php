<?php

namespace App\Entity;

use App\Repository\ChannelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChannelRepository::class)]
class Channel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 10)]
    private ?string $allowed_media_type = null;

    #[ORM\Column]
    private ?int $max_progression_allowed = null;

    #[ORM\ManyToOne(inversedBy: 'createdChannels')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $creator = null;

    /**
     * @var Collection<int, ChannelUser>
     */
    #[ORM\OneToMany(targetEntity: ChannelUser::class, mappedBy: 'channel', orphanRemoval: true)]
    private Collection $channelUsers;

    /**
     * @var Collection<int, ChannelLike>
     */
    #[ORM\OneToMany(targetEntity: ChannelLike::class, mappedBy: 'channel_id', orphanRemoval: true)]
    private Collection $channelLikes;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'channel_id', orphanRemoval: true)]
    private Collection $messages;

    public function __construct()
    {
        $this->channelUsers = new ArrayCollection();
        $this->channelLikes = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAllowedMediaType(): ?string
    {
        return $this->allowed_media_type;
    }

    public function setAllowedMediaType(string $allowed_media_type): static
    {
        $this->allowed_media_type = $allowed_media_type;

        return $this;
    }

    public function getMaxProgressionAllowed(): ?int
    {
        return $this->max_progression_allowed;
    }

    public function setMaxProgressionAllowed(int $max_progression_allowed): static
    {
        $this->max_progression_allowed = $max_progression_allowed;

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): static
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * @return Collection<int, ChannelUser>
     */
    public function getChannelUsers(): Collection
    {
        return $this->channelUsers;
    }

    public function addChannelUser(ChannelUser $channelUser): static
    {
        if (!$this->channelUsers->contains($channelUser)) {
            $this->channelUsers->add($channelUser);
            $channelUser->setChannel($this);
        }

        return $this;
    }

    public function removeChannelUser(ChannelUser $channelUser): static
    {
        if ($this->channelUsers->removeElement($channelUser)) {
            // set the owning side to null (unless already changed)
            if ($channelUser->getChannel() === $this) {
                $channelUser->setChannel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ChannelLike>
     */
    public function getChannelLikes(): Collection
    {
        return $this->channelLikes;
    }

    public function addChannelLike(ChannelLike $channelLike): static
    {
        if (!$this->channelLikes->contains($channelLike)) {
            $this->channelLikes->add($channelLike);
            $channelLike->setChannelId($this);
        }

        return $this;
    }

    public function removeChannelLike(ChannelLike $channelLike): static
    {
        if ($this->channelLikes->removeElement($channelLike)) {
            // set the owning side to null (unless already changed)
            if ($channelLike->getChannelId() === $this) {
                $channelLike->setChannelId(null);
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
            $message->setChannelId($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getChannelId() === $this) {
                $message->setChannelId(null);
            }
        }

        return $this;
    }
}
