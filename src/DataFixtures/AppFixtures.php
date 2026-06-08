<?php

namespace App\DataFixtures;

use App\Entity\Channel;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('Axel');
        $user->setEmail('axel@onepiece-only.local');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword('password');
        $user->setMediaType('both');
        $user->setProgressionAnime(1100);
        $user->setProgressionManga(1120);
        $manager->persist($user);

        $channel1 = new Channel();
        $channel1->setName('general');
        $channel1->setDescription('Salon général sans spoil');
        $channel1->setAllowedMediaType('both');
        $channel1->setMaxProgressionAllowed(0);
        $channel1->setCreator($user);
        $manager->persist($channel1);

        $channel2 = new Channel();
        $channel2->setName('zone-scans');
        $channel2->setDescription('Ici on parle des derniers chapitres.');
        $channel2->setAllowedMediaType('Manga');
        $channel2->setMaxProgressionAllowed(1184);
        $channel2->setCreator($user);
        $manager->persist($channel2);

        $manager->flush();
    }
}
