<?php

namespace App\Controller;

use App\Form\RegisterType;
use Doctrine\ORM\Query\QueryException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * @Route("/user/login", name="login")
     */
    public function login(AuthenticationUtils $auth)
    {
        $error = $auth->getLastAuthenticationError();
        $user = $auth->getLastUsername();

        return $this->render('user/login.html.twig', [
            'error' => $error,
            'user'  => $user
        ]);
    }

    /**
     * @Route("/user/register", name="register")
     */
    public function register(\Symfony\Component\HttpFoundation\Request $request, \Doctrine\ORM\EntityManagerInterface $em, \App\Repository\UsersRepository $ur, \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $encoder)
    {
        $register = $this->createForm(RegisterType::class);
        $register->handleRequest($request);

        if ($register->isSubmitted() && $register->isValid()) {
            $data = $register->getData();

            try {
                $check = $ur->userExistance([$data['username'], $data['email']]);

                if (!$check) {
                    $user = new \App\Entity\Users();
                    $user->setUsername($data['username']);
                    $user->setPassword($encoder->encodePassword($user, $data['password']));
                    $user->setEmail($data['email']);
                    $user->setName($data['name']);
                    $user->setRoles(['ROLE_USER']);

                    $em->persist($user);
                    $em->flush();

                    return $this->redirectToRoute('login', []);
                } else {
                    $this->addFlash('danger', "User with such credentials already exists");
                }
            } catch (QueryException $qe) {
                throw "Error occured during register process. Please try again. Error message: {$qe->getMessage()}";
            }
        }

        return $this->render('user/register.html.twig', [
            'register' => $register->createView()
        ]);
    }

    /**
     * @Route("/user/logout", name="logout")
     */
    public function logout()
    {
        return $this->redirectToRoute('login', []);
    }
}
