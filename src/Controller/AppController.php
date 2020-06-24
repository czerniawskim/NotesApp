<?php

namespace App\Controller;

use App\Form\NoteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function homepage()
    {
        return $this->render('app/index.html.twig', []);
    }

    /**
     * @Route("/new", name="newNote", methods={"POST"})
     */
    public function newNote()
    {
        try {
            $note = new \App\Entity\Notes();
            $note->setContent($_POST['content']);
            $note->setCreatedAt(new \DateTime());
            $note->setOwner($this->getUser());

            $this->em->persist($note);
            $this->em->flush();

            return $this->redirectToRoute('homepage', []);
        } catch (\Doctrine\ORM\Query\QueryException $qe) {
            throw $qe->getMessage();
        }
    }

    /**
     * @Route("/edit/{id}", name="editNote")
     */
    public function editNote(int $id, \Symfony\Component\HttpFoundation\Request $request, \App\Repository\NotesRepository $nr)
    {
        $note = $nr->findOneBy(['id' => $id]);
        if ($this->getUser()->getUsername() !== $note->getOwner()->getUsername()) {
            $this->addFlash('danger', 'You can\'t update this');
            return $this->redirectToRoute('homepage', []);
        }

        $update = $this->createForm(NoteType::class, null, ['id' => $id]);
        $update->handleRequest($request);

        if ($update->isSubmitted() && $update->isValid()) {
            $data = $update->getData();

            $note->setContent($data['content']);

            $this->em->flush();

            return $this->redirectToRoute('homepage', []);
        }

        return $this->render('app/edit.html.twig', [
            'update' => $update->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="deleteNote", methods={"POST"})
     */
    public function deleteNote(int $id, \App\Repository\NotesRepository $nr)
    {
        try {
            $note = $nr->findOneBy(['id' => $id]);

            if ($note->getOwner()->getUsername() === $this->getUser()->getUsername()) {
                $this->em->remove($note);
                $this->em->flush();
            } else {
                $this->addFlash('danger', 'You aren\'t owner of this element');
            }
        } catch (\Doctrine\ORM\Query\QueryException $qe) {
            throw $qe->getMessage();
        }
    }
}
