<?php

namespace App\Controller;

use App\Entity\ResetPassword;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/mot-de-passe-oublie', name: 'app_reset_password')]
    public function index(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        $email = $request->get('email');
        if ($email) {
            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($email);
            if ($user) {
                $rest_password = new ResetPassword();
                $rest_password->setUser($user);
                $rest_password->setToken(uniqid());
                $rest_password->setCreatedAt(new DateTime());
                $this->entityManager->persist($rest_password);
                $this->entityManager->flush();
                // envoyer email
            }
        }
        return $this->render('reset_password/index.html.twig');
    }
    #[Route('/modifier-mon-mot-de-passe-oublie/{token}', name: 'app_update_password')]
    public function update(): Response
    {
        return $this->render('reset_password/index.html.twig');
    }
}
