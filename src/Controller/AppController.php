<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Notes;
use Doctrine\ORM\EntityManagerInterface;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="appHomepage")
     */
    public function app(SessionInterface $session)
    {
        $user=$session->get('user');
        if(!$user)
        {
            return $this->redirectToRoute('userLogIn', []);
        }
        
        $notes=$user->getNotes();
        return $this->render('app/index.html.twig', [
            'notes'=>$notes,
            'name'=>$user->getLogin()
        ]);
    }

    /**
     * @Route("/new")
     */
    public function new(EntityManagerInterface $em, SessionInterface $s)
    {
        $user=$s->get('user');
        $now=new \DateTime();
        $note=new Notes();
        $note->setContent("I'm first note!");
        $note->setCreatedAt($now);
        $note->setUser($user);

        $em->persist($note);
        $em->flush();

        return $this->redirectToRoute('appHomepage', []);
    }
}
