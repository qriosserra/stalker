<?php

// src/Controller/SteamController.php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SteamController extends AbstractController
{
    #[Route('/steam/connect', name: 'steam_connect')]
    public function connect(OAuth2Client $steamClient)
    {
        return $steamClient->redirect();
    }

    #[Route('/steam/check', name: 'steam_check')]
    public function check(#[Autowire('%steam_api%')]
                          string $steamApi,
                          Request $request): RedirectResponse
    {
        $requestParams = $request->query->all();

        if (isset($requestParams['openid_claimed_id'])) {
            $claimedId = $requestParams['openid_claimed_id'];
            $steamId = basename($claimedId);


            $url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$steamApi}&steamids={$steamId}";


            $httpClient = HttpClient::create();
            $response = $httpClient->request('GET', $url);


            if ($response->getStatusCode() === 200) {
                $data = $response->toArray();

                var_dump($data);
                exit();
            }


            return $this->redirectToRoute('homepage');
        }
        return $this->redirectToRoute('error_page');
    }
}
