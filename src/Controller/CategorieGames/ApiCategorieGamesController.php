<?php

namespace App\Controller\CategorieGames;

use App\Entity\Games;
use App\Repository\CategorieGamesRepository;
use App\Repository\GamesRepository;
use App\Repository\QuestionRepository;
use App\Repository\ThemeRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ApiCategorieGamesController extends AbstractFOSRestController
{
    /**
     * @Route("/api/question/affich/byCategorie/{id}", name="Themed_show" ,methods={"GET"})
     * @param CategorieGamesRepository $categorieGamesRepository
     * @param QuestionRepository $questionRepository
     * @param int $id
     * @return Response
     */
    public function show(CategorieGamesRepository $categorieGamesRepository,
                         QuestionRepository $questionRepository,
                         int $id
    ): Response
    {
        $categorieGame = $categorieGamesRepository->findOneBy(['games' => $id]);
        $questionsList = $categorieGame->getQuestionsList();
        $questions = explode(',', $questionsList);
        $allQuestionInfo = [];
        foreach ($questions as $question) {
            $questionInfo = $questionRepository->findOneBy(['id' => $question]);
//            array_push($allQuestionInfo, $questionInfo);
            $allQuestionInfo["question"] = (['id' => $questionInfo->getId(), 'name' => $questionInfo->getQuestion(), 'theme_id' => $questionInfo->getTheme()->getId(), 'theme' => $questionInfo->getTheme()->getName(), 'category' => $questionInfo->getTheme()->getCategorie()->getNameCategorie(), 'category_id' => $questionInfo->getTheme()->getCategorie()->getId(), "reponses" => []]);
        }
        $reponses = $questionInfo->getReponse();
        foreach ($reponses as $reponse) {
            $reponse = ["id" => $reponse->getId(), "reponse" => $reponse->getReponse(),"etat"=>$reponse->getEtat()];
            $allQuestionInfo["question"]["reponses"] = $reponse;
        }
        $view = $this->view($allQuestionInfo, 200);
        return $this->handleView($view);

    }

//
//    /**
//     * @Route("/api/question/affich/byQuestio/{id}", name = "question_id_show",methods={"GET"})
//     * @param int $id
//     * @param CategorieGamesRepository $categorieGamesRepository
//     * @param QuestionRepository $questionRepository
//     * @return Response
//     */
//    public function affiche(int $id,
//                            CategorieGamesRepository $categorieGamesRepository,
//                            QuestionRepository $questionRepository
//    ): Response
//    {
//        $games = $categorieGamesRepository->findBy(["games" => $id]);
//        if ($games == null) {
//            throw $this->createNotFoundException(
//                'No games found for id ' . $id
//            );
//        }
//        $allQuestionInfo = [];
//        foreach ($games as $game) {
//            $questionsList = $game->getQuestionsList();
//            $questionsIds = explode(",", $questionsList);
//            foreach ($questionsIds as $question_id) {
//                $questionInfo = $questionRepository->findOneBy(['id' => $question_id]);
//                $questions = $questionInfo->getReponse();
//                $result = [];
//                foreach ($questions as $rep) {
//                    $result[] = ['id' => $rep->getId(), 'reponse' => $rep->getReponse()];
//                }
//                $allQuestionInfo[] = ['id' => $questionInfo->getId(), 'name' => $questionInfo->getQuestion(),
//                    'theme_id' => $questionInfo->getTheme()->getId(), 'theme' => $questionInfo->getTheme()->getName(),
//                    'category' => $questionInfo->getTheme()->getCategorie()->getNameCategorie(),
//                    'category_id' => $questionInfo->getTheme()->getCategorie()->getId(), "reponses" => $result];
//            }
//        }
//        $view = $this->view($allQuestionInfo, 200);
//        return $this->handleView($view);
//    }


    /**
     * @Route("/api/question/affich/byQuestion/{id}", name = "question_id_show",methods={"POST"})
     * @param int $id
     * @param CategorieGamesRepository $categorieGamesRepository
     * @param QuestionRepository $questionRepository
     * @param GamesRepository $gamesRepository
     * @param Request $request
     * @return Response
     */
    public function affiche(int $id,
                            CategorieGamesRepository $categorieGamesRepository,
                            QuestionRepository $questionRepository,
                            GamesRepository $gamesRepository,
                            Request $request
    ): Response
    {
        $test = $request->request->getBoolean('connect');
//        var_dump($test);
        $save = $this->getDoctrine()->getManager();
        $gamees = $gamesRepository->findOneBy(['id' => $id]);
        if ($gamees == null) {
            throw $this->createNotFoundException(
                'No games found for id ' . $id
            );
        }
        $games = $categorieGamesRepository->findBy(["games" => $id]);
        if ($games == null) {
            throw $this->createNotFoundException(
                'No games found for id ' . $id
            );
        }
        if ($test) {
            $allQuestionInfo = [];
            foreach ($games as $game) {
                $questionsList = $game->getQuestionsList();
                $questionsIds = explode(",", $questionsList);
                $number = (int)$questionsIds[array_key_first($questionsIds)];
                $list = implode(',', array_slice($questionsIds, 1, count($questionsIds) - 1));
                $qugames = $save->getRepository(Games::class)->find($id);
                $qugames->setCorantQuestion($number);
                $qugames->setCorantList($list);
                $save->persist($qugames);
                $save->flush();
                $questionInfo = $questionRepository->findOneBy(['id' => $questionsIds]);
                $questions = $questionInfo->getReponse();
                $result = [];
                foreach ($questions as $rep) {
                    $result[] = ['id' => $rep->getId(), 'reponse' => $rep->getReponse(),'etat'=>$rep->getEtat()];
                }
                $allQuestionInfo[] = ['id' => $questionInfo->getId(), 'name' => $questionInfo->getQuestion(),
                    'theme_id' => $questionInfo->getTheme()->getId(), 'theme' => $questionInfo->getTheme()->getName(),
                    'category' => $questionInfo->getTheme()->getCategorie()->getNameCategorie(),
                    'category_id' => $questionInfo->getTheme()->getCategorie()->getId(), "reponses" => $result];
            }
            $view = $this->view($allQuestionInfo, 200);
            return $this->handleView($view);
        } else {

            $allQuestionInfo = [];


            $questionsIds = $gamees->getCorantList();

            $questionsIds = explode(",", $questionsIds);

            $number = (int)$questionsIds[array_key_first($questionsIds)];

            $list = implode(',', array_slice($questionsIds, 1, count($questionsIds) - 1));

            $qugames = $save->getRepository(Games::class)->find($id);
            $qugames->setCorantQuestion($number);
            $qugames->setCorantList($list);

            $save->persist($qugames);
            $save->flush();

            $questionInfo = $questionRepository->findOneBy(['id' => $questionsIds]);
            $questions = $questionInfo->getReponse();
            $result = [];
            foreach ($questions as $rep) {
                $result[] = ['id' => $rep->getId(), 'reponse' => $rep->getReponse(),'etat'=>$rep->getEtat()];
            }
            $allQuestionInfo[] = ['id' => $questionInfo->getId(), 'name' => $questionInfo->getQuestion(),
                'theme_id' => $questionInfo->getTheme()->getId(), 'theme' => $questionInfo->getTheme()->getName(),
                'category' => $questionInfo->getTheme()->getCategorie()->getNameCategorie(),
                'category_id' => $questionInfo->getTheme()->getCategorie()->getId(), "reponses" => $result];

            $view = $this->view($allQuestionInfo, 200);
            return $this->handleView($view);
//            $allQuestionInfo = [];
//            foreach ($gamees as $game) {
//                $questionsIds = $game->getCorantList();
//                $questionsIds = explode(",", $questionsIds);
//                $number = (int)(array_slice($questionsIds, 0, 1));
//                $list = implode(',', array_slice($questionsIds, 1, count($questionsIds) - 1));
//                $qugames = $save->getRepository(Games::class)->find($id);
//                $qugames->setCorantQuestion($number);
//                $qugames->setCorantList($list);
//
//                $save->persist($qugames);
//                $save->flush();
//                $questionInfo = $questionRepository->findOneBy(['id' => $questionsIds]);
//                $questions = $questionInfo->getReponse();
//                $result = [];
//                foreach ($questions as $rep) {
//                    $result[] = ['id' => $rep->getId(), 'reponse' => $rep->getReponse()];
//                }
//                $allQuestionInfo[] = ['id' => $questionInfo->getId(), 'name' => $questionInfo->getQuestion(),
//                    'theme_id' => $questionInfo->getTheme()->getId(), 'theme' => $questionInfo->getTheme()->getName(),
//                    'category' => $questionInfo->getTheme()->getCategorie()->getNameCategorie(),
//                    'category_id' => $questionInfo->getTheme()->getCategorie()->getId(), "reponses" => $result];
//            }
//            $view = $this->view($allQuestionInfo, 200);
//            return $this->handleView($view);


        }


    }


    /**
     * @Route("/api/question/affich/byQuestionMenus/{id}", name = "question affich menus",methods={"GET"})
     * @param int $id
     * @param CategorieGamesRepository $categorieGamesRepository
     * @param QuestionRepository $questionRepository
     * @param ThemeRepository $themeRepository
     * @return Response
     */
    public function affich(int $id,
                           CategorieGamesRepository $categorieGamesRepository,
                           QuestionRepository $questionRepository,
                           ThemeRepository $themeRepository
    ): Response
    {
        $games = $categorieGamesRepository->findBy(["games" => $id]);
        if ($games == null) {
            throw $this->createNotFoundException(
                'No themes found for id ' . $id
            );
        }
//        dd($games);
        $allQuestionInfo = [];
        $allThemeInfo = [];
        foreach ($games as $game) {
            $questionsList = $game->getQuestionsList();
            $questionsIds = explode(",", $questionsList);
            foreach ($questionsIds as $question_id) {
                $questionInfo = $questionRepository->findOneBy(['id' => $question_id]);
                $result = [];
                $questions = $questionInfo->getReponse();
                foreach ($questions as $rep) {
                    $result[] = ['id' => $rep->getId(), 'reponse' => $rep->getReponse(),'etat'=>$rep->getEtat()];
                }
                if ($questionInfo->getTheme()->getCategorie()->getNameCategorie() == "Menus") {
                    $allQuestionInfo[] = ['id' => $questionInfo->getId(), 'name' => $questionInfo->getQuestion(),
                        'theme_id' => $questionInfo->getTheme()->getId(), 'theme' => $questionInfo->getTheme()->getName(), 'photo' => $questionInfo->getTheme()->getPhoto(),
                        'category' => $questionInfo->getTheme()->getCategorie()->getNameCategorie(),
                        'category_id' => $questionInfo->getTheme()->getCategorie()->getId(), "reponses" => $result];
                }
                if ($questionInfo->getTheme()->getCategorie()->getNameCategorie() == "Menus") {
                    $allThemeInfo[] = $questionInfo->getTheme()->getId();
                }
            }
        }
        $allThemeInfo = array_values(array_unique($allThemeInfo));
        $result = [];
        foreach ($allThemeInfo as $themeID) {

            $theme = $themeRepository->findPictureAndNAmeByID($themeID);
            foreach ($allQuestionInfo as $question) {
                if ($question['theme_id'] == $themeID) {
                    $theme["questions"][] = $question;
                }
            }
            $result[] = $theme;
        }
        $view = $this->view($result, 200);
        return $this->handleView($view);
    }


    /**
     * @Route("/api/question/affich/Questionparclick/{id}", name = "theme affich menus",methods={"GET"})
     * @param int $id
     * @param QuestionRepository $questionRepository
     * @param GamesRepository $gamesRepository
     * @return Response
     */
    public function affichTheme(int $id,
                                QuestionRepository $questionRepository,
                                GamesRepository $gamesRepository
    ): Response
    {
        $games = $gamesRepository->findOneBy(['id' => $id]);
        if ($games == null) {
            throw $this->createNotFoundException(
                'No games found for id ' . $id
            );
        }
        $allQuestionInfo = [];
        $games = $gamesRepository->findOneBy(['id' => $id]);
        $questionsIds = $games->getCorantQuestion();
        $questionInfo = $questionRepository->findOneBy(['id' => $questionsIds]);
        $questions = $questionInfo->getReponse();
        $result = [];
        foreach ($questions as $rep) {
            $result[] = ['id' => $rep->getId(), 'reponse' => $rep->getReponse(),'etat'=>$rep->getEtat()];
        }
        $allQuestionInfo[] = ['id' => $questionInfo->getId(), 'name' => $questionInfo->getQuestion(),
            'theme_id' => $questionInfo->getTheme()->getId(), 'theme' => $questionInfo->getTheme()->getName(),
            'category' => $questionInfo->getTheme()->getCategorie()->getNameCategorie(),
            'category_id' => $questionInfo->getTheme()->getCategorie()->getId(), "reponses" => $result];
        $view = $this->view($allQuestionInfo, 200);
        return $this->handleView($view);
    }
}
