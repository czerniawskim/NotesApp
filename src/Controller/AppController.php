<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Notes;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotesRepository;
use App\Repository\UsersRepository;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="appHomepage")
     */
    public function app(SessionInterface $session, NotesRepository $nR)
    {
        $user=$session->get('user');
        if(!$user)
        {
            return $this->redirectToRoute('userLogIn', []);
        }
        
        $id=$user->getId();
        $notes=$nR->findBy(array('user'=>$id), array('createdAt'=>'DESC'));
        return $this->render('app/index.html.twig', [
            'notes'=>$notes
        ]);
    }

    /**
     * @Route("/new/{content}", methods={"POST"})
     */
    public function new($content, EntityManagerInterface $em, SessionInterface $session, UsersRepository $uR, NotesRepository $nR)
    {
        $user = $uR->findBy(['Login'=>$session->get('user')->getLogin()]);

        $note = new Notes();
        $note->setContent($content);
        $note->setCreatedAt(new \DateTime());
        $note->setUser($user[0]);

        $em->persist($note);
        $em->flush();
        
        return new Response();
    }

    /**
     * @Route("/delete/{id}", methods={"POST"})
     */
    public function delete($id, EntityManagerInterface $em, NotesRepository $nR)
    {
        $note=$nR->findBy(['id'=>$id]);

        if(!$note)
        {
            $this->addFlash(
                'danger',
                'Something went wrong'
            );
        }

        $em->remove($note[0]);
        $em->flush();

        return new Response();
    }

    /**
     * @Route("/edit/{id}/{content}", methods={"POST"})
     */
    public function edit($id, $content, EntityManagerInterface $em, NotesRepository $nR)
    {
        $note = $nR->findBy(['id'=>$id]);

        if(!$note)
        {
            $this->addFlash(
                'danger',
                'Something went wrong'
            );
        }

        $note[0]->setContent($content);

        $em->flush();

        return new Response();
    }
}
