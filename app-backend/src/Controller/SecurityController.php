<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly DenormalizerInterface&NormalizerInterface $serializer
    ) {
    }

    #[Route('/api/login', name: 'app_login', methods: ['POST'],)]
    public function login(#[CurrentUser] ?User $user): Response
    {
        return $this->json($this->serializer->normalize($user, 'json', ['groups' => 'public']));
    }

    #[Route('/api/userinfo', name: 'app_userinfo', methods: ['GET'])]
    public function userInfo(#[CurrentUser] ?User $user): Response
    {
        return $this->json($this->serializer->normalize($user, 'json', ['groups' => 'user_details']));
    }

    //todo: add logout route
}