<?php

namespace App\Entity;

use App\Repository\ChannelLikeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChannelLikeRepository::class)]
class ChannelLike
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'channelLikes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Channel $channel_id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $liked_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChannelId(): ?Channel
    {
        return $this->channel_id;
    }

    public function setChannelId(?Channel $channel_id): static
    {
        $this->channel_id = $channel_id;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getLikedAt(): ?\DateTimeImmutable
    {
        return $this->liked_at;
    }

    public function setLikedAt(\DateTimeImmutable $liked_at): static
    {
        $this->liked_at = $liked_at;

        return $this;
    }
}
