<?php

namespace App\Repository;

use App\Entity\Channel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Channel>
 */
class ChannelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Channel::class);
    }

    public function findSafeChannels(string $format, int $progress): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.allowed_media_type = :format')
            ->andWhere('c.max_progression_allowed <= :progress')
            ->setParameter('format', $format)
            ->setParameter('progress', $progress)
            ->getQuery()
            ->getResult();
    }

    public function findChannelsWithMessagesByUser(\App\Entity\User $user): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.messages', 'm')
            ->where('m.user_id = :user')
            ->setParameter('user', $user)
            ->distinct()
            ->getQuery()
            ->getResult();
    }
}
