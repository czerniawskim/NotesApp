<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
        if(!$user)
        {
            return $this->redirectToRoute('userLogIn', []);
        }
        
        $id=$user->getId();
        $notes=$nR->findBy(['user'=>$id]);
        return $this->render('app/index.html.twig', [
            'notes'=>$notes,
            'name'=>$user->getLogin()
        ]);
    }

    /**
     * @Route("/new", name="noteAdd")
     */
    public function new(EntityManagerInterface $em, SessionInterface $s, Request $request)
    {
        $form=$this->createFormBuilder()
        ->add('Content', TextareaType::class,[
            'attr'=>[
                'placeholder'=>'Note content',
                'class'=>'ninpt'
            ]
        ])
        ->add('Create', SubmitType::class,[
            'attr'=>[
                'class'=>'nsub'
            ]
        ])
        ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $content=$form->getData();
            $content=$content["Content"];
            $user=$s->get('user');
            $now=new \DateTime();
            $note=new Notes();
            $note->setContent($content);
            $note->setCreatedAt($now);
            $note->setUser($user);

            $em->merge($note);
            $em->flush();

            return $this->redirectToRoute('appHomepage', []);
        }
        return $this->render('app/new.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="noteDelete", methods={"POST"})
     */
    public function delete($id, EntityManagerInterface $em)
    {
        $note=$this->getDoctrine()->getRepository(Notes::class)->find($id);

        if(!$note)
        {
            $this->addFlash(
                'danger',
                'Something went wrong'
            );
        }

        $em->remove($note);
        $em->flush();
        $em->clear();

        return new Response();
    }
}
