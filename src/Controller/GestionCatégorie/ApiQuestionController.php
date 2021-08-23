<?php

namespace App\Controller\GestionCatégorie;



use App\Entity\Question;
use App\Repository\QuestionRepository;
use App\Repository\ThemeRepository;
use DateTime;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method transformJsonBody($request)
 */
class ApiQuestionController extends AbstractFOSRestController

{

    /**
     * @Route("/api/question/affichall", name="Affiche_List_question",methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $question = $this->getDoctrine()->getRepository(Question::class)->findAll();

        $view = $this->view($question, 200);
        return $this->handleView($view);


    }

    /**
     * @Route("/api/question/affich/byTheme/{id}", name="question_show")
     * @param QuestionRepository $questionRepository
     * @param int $id
     * @return Response
     */
    public function show(QuestionRepository $questionRepository, int $id): Response
    {
        $question = $questionRepository->findBy(["theme"=>$id]);
        if (!$question) {
            throw $this->createNotFoundException(
                'No Question found for id ' . $id
            );
        }
        $view = $this->view($question, 200);
        return $this->handleView($view);
    }
    /**
     * @Route("/api/question/add", name="insert_question",methods={"POST"})
     * @param Request $request
     * @param ThemeRepository $themeRepository
     * @return Response
     */

    public function add(Request $request, ThemeRepository $themeRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        $theme_id = $request->request->get('theme_id');
        $questions = $request->request->get('question');
        $theme = $themeRepository->find($theme_id);
        $question = new Question();
        $question->setQuestion($questions);

        $question->setTheme($theme);
        $question->setDateCreation(new DateTime());
        $question->setDateModif(new DateTime());
        $em->persist($question);
        $em->flush();
        $view = $this->view($question, 200);
        return $this->handleView($view);
    }

    /**
     * @Route("/api/question/edit/{id}",name="edit_question",methods={"POST"})
     * @param Request $request
     * @param int $id
     * @param ThemeRepository $themeRepository
     * @return Response
     */
    public function update(Request $request, int $id, ThemeRepository $themeRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        $theme_id = $request->request->get('theme_id');
        $questions = $request->request->get('question');
        $theme = $themeRepository->find($theme_id);
        $question = $em->getRepository(Question::class)->find($id);
        $question->setQuestion($questions ?? $question->getQuestion());
        $question->setTheme($theme);
        $question->setDateCreation($question->getDateCreation());
        $question->setDateModif(new DateTime());
        $em->persist($question);
        $em->flush();
        $view = $this->view($question, 200);
        return $this->handleView($view);


    }

    /**
     * @Route("/api/question/delete/{id}",methods={"DELETE"})
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $question = $entityManager->getRepository(Question::class)->find($id);

        if (!$question) {
            throw $this->createNotFoundException(
                'No Question found for id ' . $id
            );
        }
        foreach ($question->getReponse() as $reponse) {
            $entityManager->remove($reponse);
        }
        $entityManager->remove($question);
        $entityManager->flush();
        $view = $this->view($question, 200);
        return $this->handleView($view);
    }




}
