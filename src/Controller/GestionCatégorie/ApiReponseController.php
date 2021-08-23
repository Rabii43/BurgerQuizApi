<?php

namespace App\Controller\GestionCatégorie;

use App\Entity\Reponse;
use App\Repository\QuestionRepository;
use App\Repository\ReponseRepository;
use DateTime;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiReponseController extends AbstractFOSRestController
{
    /**
     * @Route("/api/reponse/affichall", name="Affiche_List_reponse",methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $reponse = $this->getDoctrine()->getRepository(Reponse::class)->findAll();

        $view = $this->view($reponse, 200);
        return $this->handleView($view);


    }

    /**
         * @Route("/api/reponse/affich/byQestion/{id}", name="reponse_show")
     * @param ReponseRepository $repository
     * @param int $id
     * @return Response
     */
    public function show(ReponseRepository $repository, int $id): Response
    {

        $question = $repository->findBy(["question"=>$id]);

        if (!$question) {
            throw $this->createNotFoundException(
                'No Reponse found for id ' . $id
            );
        }

        $view = $this->view($question, 200);
        return $this->handleView($view);
    }

    /**
     * @Route("/api/reponse/add", name="insert_reponse",methods={"POST"})
     * @param Request $request
     * @param QuestionRepository $questionRepository
     * @return Response
     */

    public function add(Request $request, QuestionRepository $questionRepository): Response
    {
        $em = $this->getDoctrine()->getManager();

        $reponses = $request->request->get('reponse');
        $etat = $request->request->get('etat');
        $etat = settype($etat, 'boolean');
        $question_id = $request->request->get('question_id');
        $question = $questionRepository->find($question_id);
        $reponse = new reponse();
        $reponse->setReponse($reponses);
        $reponse->setEtat($etat);
        $reponse->setQuestion($question);
        $reponse->setDateCreation(new DateTime());
        $reponse->setDateModif(new DateTime());
        $em->persist($reponse);
        $em->flush();
        $view = $this->view($reponse, 200);
        return $this->handleView($view);
    }

    /**
     * @Route("/api/reponse/edit/{id}",name="edit_reponse",methods={"POST"})
     * @param Request $request
     * @param int $id
     * @param QuestionRepository $questionRepository
     * @return Response
     */
    public function update(Request $request, int $id, QuestionRepository $questionRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        $reponses = $request->request->get('reponse');
        $etat = $request->request->get('etat');

        var_dump($etat);

        $question_id = $request->request->get('question_id');
        $question = $questionRepository->find($question_id);
        $reponse = $em->getRepository(Reponse::class)->find($id);
        $reponse->setReponse($reponses ?? $reponse->getReponse());

        $reponse->setEtat($etat ?? $reponse->getEtat());
        $reponse->setQuestion($question);

        $reponse->setDateModif(new DateTime());
        $em->persist($reponse);
        $em->flush();
        $view = $this->view($reponse, 200);
        return $this->handleView($view);


    }

    /**
     * @Route("/api/reponse/delete/{id}",methods={"DELETE"})
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $reponse = $entityManager->getRepository(Reponse::class)->find($id);

        if (!$reponse) {
            throw $this->createNotFoundException(
                'No Reponse found for id ' . $id
            );
        }
        $entityManager->remove($reponse);
        $entityManager->flush();
        $view = $this->view($reponse, 200);
        return $this->handleView($view);
    }



}
