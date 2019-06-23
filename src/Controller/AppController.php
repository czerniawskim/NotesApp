<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Notes;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotesRepository;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="appHomepage")
     */
    public function app(SessionInterface $session, NotesRepository $nR)
    {
        $user=$session->get('user');
        $id=$user->getId();
        if(!$user)
        {
            return $this->redirectToRoute('userLogIn', []);
        }
        
        $notes=$nR->findBy(['user'=>$id]);
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
        $note->setContent("I'm second note!");
        $note->setCreatedAt($now);
        $note->setUser($user);

        $em->merge($note);
        $em->flush();

        return $this->redirectToRoute('appHomepage', []);
    }
}
