<?php

namespace App\Controller\Setting;

use App\Entity\CategorieGames;
use App\Entity\Equipe;
use App\Entity\Games;
use App\Repository\CategorieGamesRepository;
use App\Repository\EquipeRepository;
use App\Repository\GamesRepository;
use App\Repository\QuestionRepository;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method transformJsonBody($request)
 */
class SettingGamesController extends AbstractFOSRestController
{
    /**
     * @Route("/api/setting/affichall", name="Affiche_List_Setting",methods={"GET"})
     * @param Request $request
     * @return ResponseAlias
     */
    public function indexAction(Request $request): ResponseAlias
    {
        $categories = $this->getDoctrine()->getRepository(Games::class)->findAll();
        $view = $this->view($categories, 200);
        return $this->handleView($view);
    }

    /**
     * @Route("/api/equipe/affich/{id}", name="equipe_show")
     * @param int $id
     * @param GamesRepository $gamesRepository
     * @param UserRepository $userRepository
     * @return Response
     */
    public function show(int $id, GamesRepository $gamesRepository, UserRepository $userRepository): Response
    {
        $games = $gamesRepository->find($id);
        if (!$games) {
            throw $this->createNotFoundException(
                'No Catégorie found for id ' . $id
            );
        }
        $game = $games->getEquipe();
        $equipe1 = $game[0]->getUseres();
        $equipe2 = $game[1]->getUseres();
        $equip1 = explode(',', $equipe1);
        foreach ($equip1 as $eq1) {

            $user = $userRepository->findOneBy(["id" => $eq1]);
            $alluser[] = ['photo' => $user->getAvatar(), 'statute' => $user->getIsconnected(), 'username' => $user->getUsername()];
            $equi1[] = ['photo' => $user->getAvatar(), 'statute' => $user->getIsconnected(), 'username' => $user->getUsername()];
        }
        $equipe2 = explode(',', $equipe2);
        foreach ($equipe2 as $eq2) {

            $user = $userRepository->findOneBy(["id" => $eq2]);
            $alluser[] = ['photo' => $user->getAvatar(), 'statute' => $user->getIsconnected(), 'username' => $user->getUsername()];
            $equi2[] = ['photo' => $user->getAvatar(), 'statute' => $user->getIsconnected(), 'username' => $user->getUsername()];
        }
        $equipe = ['equipe1' => $equi1, 'equipe2' => $equi2,'all'=>$alluser];
        $view = $this->view($equipe, 200);
        return $this->handleView($view);
    }
    /**
     * @param string $to
     * @param Swift_Mailer $mailer
     * @return JsonResponse
     */


    /**
     * @Route("/api/setting/add", name="Setting_games",methods={"POST"})
     * @param QuestionRepository $questionRepository
     * @param Swift_Mailer $mailer
     * @param Request $request
     * @param UserRepository $userRepository
     * @param CategorieGamesRepository $categorieGamesRepository
     * @param GamesRepository $gamesRepository
     * @param EquipeRepository $equipeRepository
     * @return Response
     */
    public function add(QuestionRepository $questionRepository, Swift_Mailer $mailer, Request $request, UserRepository $userRepository, CategorieGamesRepository $categorieGamesRepository, GamesRepository $gamesRepository, EquipeRepository $equipeRepository): Response
    {
        $dd = $this->getDoctrine()->getManager();
        $animateur = $request->request->get('animateur');
        $responsableEq1 = $request->request->get('responsable_One');
        $responsableEq2 = $request->request->get('responsable_Two');
        $NameEq1 = $request->request->get('nameEquipe_One');
        $NameEq2 = $request->request->get('nameEquipe_Two');
        $titleEquipe1 = $request->request->get('titleEquipe_One');
        $titleEquipe2 = $request->request->get('titleEquipe_Two');
        $usersOne = $request->request->get('users_One');
        $usersTwo = $request->request->get('users_Two');
        $questions = $request->request->get('questions');
        $allThemeInfo = $request->request->get('alltheme');
        $usersOne = implode(',', array($usersOne));
        $usersTwo = implode(',', array($usersTwo));
        $alluserOne = $usersOne;
        $alluserOne = explode(",", $alluserOne);
        $alluserTwo = $usersTwo;
        $alluserTwo = explode(",", $alluserTwo);
//        $questionCherchTheme = explode(",", $questions);
////        var_dump($questionCherchTheme);
//        $theme = [];
//        $questionCherchTheme = array_unique($questionCherchTheme);
//
//        foreach ($questionCherchTheme as $questionCherchThem) {
//            $themeInfo = $questionRepository->find(['id' => $questionCherchThem]);
//            $theme[] = ($themeInfo->getTheme()->getPhoto());
//        }
//        $theme=array_unique($theme);
        $games = new Games();
        $games->setAnimateur($animateur);
        $games->setDateCreation(new \DateTime());
        $games->setDateJeu(new \DateTime());
        $dd->persist($games);
        $dd->flush();
        $games_id = $games->getId();
        $games_id = $gamesRepository->find($games_id);
        $equipe1 = new Equipe();
        $equipe1->setResponsable($responsableEq1);
        $equipe1->setNameEq($NameEq1);
        $equipe1->setTitle($titleEquipe1);
        $equipe1->setUseres($usersOne);
        $equipe1->setGames($games_id);
        $equipe2 = new Equipe();
        $equipe2->setResponsable($responsableEq2);
        $equipe2->setNameEq($NameEq2);
        $equipe2->setTitle($titleEquipe2);
        $equipe2->setUseres($usersTwo);
        $equipe2->setGames($games_id);

        $questions = implode(',', array($questions));
        $categorieGames = new CategorieGames();
        $categorieGames->setGames($games_id);
        $categorieGames->setQuestionsList($questions);

        $dd->persist($categorieGames);
        $dd->flush();
        $dd->persist($equipe1);
        $dd->flush();
        $dd->persist($equipe2);
        $dd->flush();
        $equipe = $equipe1->getId();
        $equipe = $equipeRepository->find($equipe);

        foreach ($alluserOne as $user) {
            $userr = $userRepository->findOneBy(["id" => $user]);
            if ($userr == null) {
                throw $this->createNotFoundException(
                    'No User found for id ' . $user
                );
            }
            $eqp = $userr->setEquipe($equipe);
            $dd->persist($eqp);
            $dd->flush();
            $to = $userr->getEmail();
            $payload = ["id" =>  $games_id->getId()];
            $token = base64_encode(json_encode($payload));
            $eq = $userr->getEquipe()->getResponsable();
            $body = str_replace('{{equipe}}', $eq, file_get_contents(__DIR__ . '/session.html'));
            $body = str_replace('{{token}}', $token, file_get_contents(__DIR__ . '/session.html'));
            $email = (new Swift_Message())
                ->setFrom('rabii12terres@gmail.com')
                ->setTo($to)
////              ->setCc('gharianioussama24@gmail.com')
//                //->bcc('bcc@example.com')
//                //->replyTo('fabien@example.com')
//                //->priority(Email::PRIORITY_HIGH)
                ->setSubject('test  send mail rabi3')
////              ->text('Sending emails is fun again!')
                ->setBody($body, 'text/html');
            $mailer->send($email);
        }
        $equip1 = $equipe2->getId();
        $equip1 = $equipeRepository->find($equip1);
        foreach ($alluserTwo as $user) {
            $userr = $userRepository->findOneBy(["id" => $user]);
            if ($userr == null) {
                throw $this->createNotFoundException(
                    'No User found for id ' . $user
                );
            }
            $eqp = $userr->setEquipe($equip1);
            $dd->persist($eqp);
            $dd->flush();
            $to = $userr->getEmail();
            $payload = ["id" =>  $games_id->getId()];
            $token = base64_encode(json_encode($payload));
            $eq = $userr->getEquipe()->getResponsable();
            $bod = str_replace('{{equipe}}', $eq, file_get_contents(__DIR__ . '/session.html'));
            $body = str_replace('{{token}}', $token, file_get_contents(__DIR__ . '/session.html'));
            $email = (new Swift_Message())
                ->setFrom('rabii12terres@gmail.com')
                ->setTo($to)
////              ->setCc('gharianioussama24@gmail.com')
//                //->bcc('bcc@example.com')
//                //->replyTo('fabien@example.com')
//                //->priority(Email::PRIORITY_HIGH)
                ->setSubject('test  send mail rabi3')
////              ->text('Sending emails is fun again!')
                ->setBody($body, 'text/html');
            $mailer->send($email);
        }
        $view = $this->view($equipe, 200);
        return $this->handleView($view);
    }

//    /**
//     * @param ReponseRepository $reponseRepository
//     * @param EquipeRepository $equipeRepository
//     * @param UserRepository $userRepository
//     * @param Request $request
//     * @return ResponseAlias
//     * @Route("api/setting/scortequipe",name="scortEquipe",methods={POST})
//     */
//    public function scortEquipe(ReponseRepository $reponseRepository, EquipeRepository $equipeRepository, UserRepository $userRepository, Request $request): ResponseAlias
//    {
//        $reponse_id = $request->request->get('reponse_id');
//        $reponse = $reponseRepository->findOneBy(['id' => $reponse_id]);
//        $resultat = $reponse->getEtat();
//
//        $view = $this->view($resultat, 200);
//        return $this->handleView($view);
//    }
}
