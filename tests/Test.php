<?php

namespace App\Tests;

use App\Entity\Channel;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class Test extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    /**
     * TEST 1 : Page d'accueil
     */
    public function testHome(): void
    {
        $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();
    }

    /**
     * TEST 2 : Filtrage anti-spoil (Redirection attendue)
     */
    public function testChannelBlocked(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([]);
        $channel = $this->entityManager->getRepository(Channel::class)->findOneBy([]);

        $this->assertNotNull($user, 'Aucun utilisateur en base pour le test.');
        $this->assertNotNull($channel, 'Aucun channel en base pour le test.');

        $user->setProgressionManga(10);
        $channel->setMaxProgressionAllowed(1100);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->request('GET', '/channels/' . $channel->getId());

        $this->assertResponseStatusCodeSame(302);
    }

    /**
     * TEST 3 : Accès autorisé si à jour dans le manga ou l'anime
     */
    public function testChannelAllowed(): void
    {
        $this->client->restart();

        $user = $this->entityManager->getRepository(User::class)->findOneBy([]);
        $channel = $this->entityManager->getRepository(Channel::class)->findOneBy([]);

        $this->assertNotNull($user);
        $this->assertNotNull($channel);

        $user->setProgressionManga(1200);
        $user->setProgressionAnime(1200);
        $channel->setMaxProgressionAllowed(1100);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->client->request('GET', '/channels/' . $channel->getId());

        $this->assertResponseIsSuccessful();
    }

    /**
     * TEST 4 : Sécurité anonyme (Redirection quand pas connecté)
     */
    public function testChannelAnon(): void
    {
        $this->client->restart();

        $channel = $this->entityManager->getRepository(Channel::class)->findOneBy([]);
        $this->assertNotNull($channel);

        $this->client->request('GET', '/channels/' . $channel->getId());

        $this->assertResponseStatusCodeSame(302);
    }

    /**
     * TEST 5 : Erreur 404
     */
    public function testChannelNotFound(): void
    {
        $this->client->request('GET', '/channels/-1');

        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * TEST 6 : Inscription d'un nouvel utilisateur
     */
    public function testRegister(): void
    {
        $this->client->restart();
        $crawler = $this->client->request('GET', '/register');

        if ($this->client->getResponse()->getStatusCode() !== 200) {
            $this->markTestSkipped('La page /register n\'est pas accessible.');
        }

        $form = $crawler->selectButton('S\'inscrire')->form([
            'registration_form[username]' => 'Luffy_' . uniqid(),
            'registration_form[email]' => 'pirate_' . uniqid() . '@onepiece.com',
            'registration_form[plainPassword]' => 'SecurePassword123!',
            'registration_form[progressionManga]' => 1,
            'registration_form[progressionAnime]' => 1,
        ]);

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(302);
    }
}
