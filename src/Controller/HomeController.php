<?php

namespace App\Controller;

use App\Repository\ChannelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, ChannelRepository $channelRepo): Response
    {
        $channels = [];
        $hasSubmitted = false;

        if ($request->query->has('format') && $request->query->has('progress')) {
            $format = $request->query->get('format');
            $progress = (int) $request->query->get('progress');
            $hasSubmitted = true;

            $channels = $channelRepo->findSafeChannels($format, $progress);
        }
        elseif ($this->getUser()) {
            $channels = $channelRepo->findChannelsWithMessagesByUser($this->getUser());
        }

        return $this->render('home/index.html.twig', [
            'channels' => $channels,
            'has_submitted' => $hasSubmitted,
        ]);
    }
}
