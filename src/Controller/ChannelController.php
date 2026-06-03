<?php

namespace App\Controller;

use App\Entity\Channel;
use App\Entity\Message;
use App\Repository\ChannelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;



class ChannelController extends AbstractController
{


    #[Route('/', name: 'app_home')]
    public function home(ChannelRepository $channelRepo): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('channel/home.html.twig', [
            'channels' => $channelRepo->findAll(),
        ]);
    }

    #[Route('/api/channels/{id}/send', name: 'api_channel_send', methods: ['POST'])]
    public function sendMessage(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        HubInterface $hub
    ): JsonResponse {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $channel = $em->getRepository(Channel::class)->find($id);
        if (!$channel) {
            return new JsonResponse(['error' => 'Canal introuvable'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $content = $data['content'] ?? '';

        if (empty($content)) {
            return new JsonResponse(['error' => 'Message vide'], 400);
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        try {
            $message = new Message();
            $message->setChannelId($channel);
            $message->setUserId($user);

            $contentNettoye = strip_tags($content);
            $message->setContent($contentNettoye);

            if (method_exists($message, 'setMediaType')) {
                try {
                    $message->setMediaType('text');
                } catch (\TypeError $e) {
                    $message->setMediaType(['text']);
                }
            }

            $message->setCreatedAt(new \DateTimeImmutable());

            $em->persist($message);
            $em->flush();

            $jsonMessage = json_encode([
                'id' => $message->getId(),
                'content' => $message->getContent(),
                'username' => $user->getUsername(),
                'createdAt' => $message->getCreatedAt()->format('H:i')
            ]);

            $topic = "https://onepiece-only.local/channels/" . $channel->getId();

            $update = new Update($topic, $jsonMessage);
            $hub->publish($update);

            return new JsonResponse(['success' => true]);

        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/channels/new', name: 'app_channel_new', methods: ['GET'])]
    public function newChannelForm(ChannelRepository $channelRepo): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('channel/channelCreate.html.twig', [
            'channels' => $channelRepo->findAll(),
        ]);
    }

    #[Route('/channels/new', name: 'app_channel_create_submit', methods: ['POST'])]
    public function handleNewChannel(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $name = $request->request->get('name');
        $description = $request->request->get('description');
        $allowedMediaType = $request->request->get('allowed_media_type', 'both');
        $maxProgressionAllowed = (int) $request->request->get('max_progression_allowed', 0);

        if (empty($name)) {
            $this->addFlash('error', 'Le nom du canal est obligatoire.');
            return $this->redirectToRoute('app_channel_new');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $channel = new Channel();
        $channel->setName(trim(strip_tags($name)));
        $channel->setDescription(trim(strip_tags($description ?? '')));

        $channel->setAllowedMediaType($allowedMediaType);
        $channel->setMaxProgressionAllowed($maxProgressionAllowed);

        $channel->setCreator($user);

        $em->persist($channel);
        $em->flush();

        return $this->redirectToRoute('app_channel_room', ['id' => $channel->getId()]);
    }

    #[Route('/channels/{id}', name: 'app_channel_room', requirements: ['id' => '\d+'])]
    public function show(int $id, ChannelRepository $channelRepo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $channel = $em->getRepository(Channel::class)->find($id);

        if (!$channel) {
            throw $this->createNotFoundException("Le canal numéro " . $id . " n'existe pas dans ta base PostgreSQL.");
        }

        $userMedia = strtolower(trim((string)$user->getMediaType()));
        $channelMedia = strtolower(trim((string)$channel->getAllowedMediaType()));

        $isAnimeOnly = ($userMedia === 'anime' && $channelMedia === 'manga');

        $channelRequiredInt = (int)$channel->getMaxProgressionAllowed();
        $userProgressionInt = 0;

        if ($channelMedia === 'manga') {
            $userProgressionInt = (int)$user->getProgressionManga();
        } else {
            $userProgressionInt = (int)$user->getProgressionAnime();
        }

        $isUnderLevel = ($userProgressionInt < $channelRequiredInt);

        if ($isAnimeOnly || $isUnderLevel) {
            $this->addFlash('danger', 'Accès refusé : Ce canal contient des spoils par rapport à ton avancement ! 🛑');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('channel/index.html.twig', [
            'channels' => $channelRepo->findAll(),
            'currentChannel' => $channel,
        ]);
    }
}
