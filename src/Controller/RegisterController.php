<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $notification = null;
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());
            if (!$search_email) {
                $password = $userPasswordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($password);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $notification = "Votre inscription c'est correctement déroulée. Vous pouvez dès à présent vous connecter à votre compte. ";
            } else {
                $notification = "l'email que vous avez rensigné existe déjà";
            }
        }
        return $this->render(
            'register/index.html.twig',
            [
                'form' => $form->createView(),
                'notification' => $notification
            ]
        );
    }
}
