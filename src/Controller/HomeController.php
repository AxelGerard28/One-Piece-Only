<?php

namespace App\Controller;

use App\Repository\ChannelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(ChannelRepository $channelRepo): Response
    {
        $allChannels = $channelRepo->findAll();
        $defaultChannel = $channelRepo->findOneBy([]);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'channel' => $allChannels,
            'currentChannel' => $defaultChannel,
            'channels' => $channelRepo->findAll(),
        ]);
    }
}
