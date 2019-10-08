<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UsersRepository;

class UserController extends AbstractController
{
    /**
     * @Route("/user/login", name="userLogIn")
     */
    public function login(Request $request, SessionInterface $session, UsersRepository $uR)
    {
        $form=$this->createFormBuilder()
        ->add('Username', TextType::class, [
            'attr'=>[
                'class'=>'linp',
                'placeholder'=>'Username'
            ]
        ])
        ->add('Password', PasswordType::class, [
            'attr'=>[
                'class'=>'linp',
                'placeholder'=>'Password'
            ]
        ])
        ->add('Login', SubmitType::class, [
            'attr'=>[
                'class'=>'lsub'
            ]
        ])
        ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $data=$form->getData();

            $exist=$uR->findBy(['Login'=>$data['Username']]);
            if($exist)
            {
                if($data['Password']===$exist[0]->getPassword())
                {
                    $session->set('user', $exist[0]);

                    return $this->redirectToRoute('appHomepage', []);
                }
                else
                {
                    $this->addFlash(
                        'danger',
                        'Wrong password'
                    );
                }
            }
            else
            {
                $this->addFlash(
                    'danger',
                    'There is no such user'
                );
            }
        }

        return $this->render('user/login.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/user/register", name="userRegister")
     */
    public function register(Request $request, EntityManagerInterface $em)
    {
        $form=$this->createFormBuilder()
        ->add('Username', TextType::class, [
            'attr'=>[
                'class'=>'rinp',
                'placeholder'=>'Username'
            ]
        ])
        ->add('Password', RepeatedType::class, [
            'type'=>PasswordType::class,
            'options'=>['attr'=>['class'=>'rinp']],
            'first_options'=>['attr'=>['placeholder'=>'Password']],
            'second_options'=>['attr'=>['placeholder'=>'Repeat password']]
        ])
        ->add('Email', TextType::class, [
            'attr'=>[
                'class'=>'rinp',
                'placeholder'=>'E-mail'
            ]
        ])
        ->add('Submit', SubmitType::class, [
            'attr'=>[
                'class'=>'rsub'
            ]
        ])
        ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $data=$form->getData();

            $user=new Users();
            $user->setLogin($data['Username']);
            $user->setPassword($data['Password']);
            $user->setEmail($data['Email']);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('userLogIn', []);
        }

        return $this->render('user/register.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/user/logout", name="userLogout")
     */
    public function logout(SessionInterface $session)
    {
        $session->clear();

        $this->addFlash(
            'success',
            'You were logged out'
        );

        return $this->redirectToRoute('userLogIn', []);
    }
}