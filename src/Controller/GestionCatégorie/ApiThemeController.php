<?php

namespace App\Controller\GestionCatégorie;

use App\Entity\Theme;
use App\Repository\CategorieRepository;
use App\Service\FileUploader;
use DateTime;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @method transformJsonBody($request)
 */
class ApiThemeController extends AbstractFOSRestController
{
    /**
     * @Route("/api/theme/affichall", name="Affiche_List_Theme",methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request ): Response
    {
        $themes = $this->getDoctrine()->getRepository(Theme::class)->findAll();
        $view = $this->view($themes, 200);
        return $this->handleView($view);

    }

    /**
     * @Route("/api/theme/affich/byCategorie/{id}", name="ThemeId_show" ,methods={"GET"})
     * @param int $id
     * @param CategorieRepository $categorieRepository
     * @return Response
     */
    public function show(int $id, CategorieRepository $categorieRepository): Response
    {
        /**
         * @var
         */
        $categories = $categorieRepository->findBy(["id" => $id]);
        if (!$categories) {
            throw $this->createNotFoundException(
                'No themes found for id ' . $id
            );
        }

        foreach ($categories as $categorie) {
     $arrayCatigorie = ["id" => $categorie->getId(), "category_name" => $categorie->getNameCategorie(), "photoCategorie" => $categorie->getPhotoCategorie(), "date_creation" => $categorie->getDateCreation(), "date_modif" => $categorie->getDateModif(), "theme" => [], "count_th" => $categorie->getCountTh()];
            $themes = $categorie->getTheme();
            foreach ($themes as $theme) {
                $questions = $theme->getQuestion();
                $arrayTheme = ["idcategorie" => $categorie->getId(), "id" => $theme->getId(), "category_name" => $categorie->getNameCategorie(), "name" => $theme->getName(), "description" => $theme->getDescription(), "photo" => $theme->getPhoto(), "color" => $theme->getColor(), "date_creation" => $theme->getDateCreation(), "date_modif" => $theme->getDateModif(),  "count" => $theme->getCount()];
                foreach ($questions as $question) {
                    $arrayQuestion = ["idTheme" => $theme->getId(), "id" => $question->getId(), "question" => $question->getQuestion(), "date_creation" => $question->getDateCreation(), "date_modif" => $question->getDateModif(), "countr" => $question->getCountr(), "reponse" => []];
                    $reponses = $question->getReponse();
                    foreach ($reponses as $reponse) {
                        $arrayQuestion["reponse"][] = ["idQuestion" => $question->getId(),"id" => $reponse->getId(), "reponse" => $reponse->getReponse(), "date_creation" => $reponse->getDateCreation(), "date_modif" => $reponse->getDateModif()];
                    }
                    $arrayTheme["question"][] = $arrayQuestion;
                }
                $arrayCatigorie["theme"][] = $arrayTheme;

            }
            $result[] = $arrayCatigorie;


        }
//dd($result);
//        $questionArray=[];
//        $result['categorie'] = $categorie;
//        foreach ($categorie as $key => $c) {
//
//            $bArray[$key] = ["id" => $c->getId(),
//                "name" => $c->getName(),
//                "description" => $c->getDescription(),
//                "photo" => $c->getPhoto(),
//                "color" => $c->getColor(),
//                "Question" => $c->getQuestion(),
//            ];
//        }
//        dd($bArray);
//        $view = $this->view($result, 200);
        $view = $this->view($result, 200);
        return $this->handleView($view);
    }


    /**
     * @Route("/api/theme/add", name="insert_theme",methods={"POST"})
     * @param Request $request
     * @param CategorieRepository $catigorieRepository
     * @param FileUploader $fileUploader
     * @return Response
     */

    public function add(Request $request, CategorieRepository $catigorieRepository,FileUploader $fileUploader): Response
    {
        $em = $this->getDoctrine()->getManager();
        $file = $fileUploader->upload($request,"image");
        $name = $request->request->get('name');
        $categorie_id = $request->request->get('categorie_id');
        $categorie = $catigorieRepository->find($categorie_id);
        $description = $request->request->get('description');
        $color = $request->request->get('color');
        $theme = new Theme();
        $theme->setDescription($description);
        $theme->setName($name);
        $theme->setPhoto($file['photo']);
        $theme->setColor($color);
        $theme->setCategorie($categorie);
        $theme->setDateCreation(new DateTime());
        $theme->setDateModif(new DateTime());
        $em->persist($theme);
        $em->flush();
        $view = $this->view($theme, 200);
        return $this->handleView($view);
    }
    /**
     * @Route("/api/theme/edit/{id}",name="edit_theme",methods={"POST"})
     * @param Request $request
     * @param int $id
     * @param CategorieRepository $catigorieRepository
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function update(Request $request, int $id, CategorieRepository $catigorieRepository,FileUploader $fileUploader): Response
    {
        $em = $this->getDoctrine()->getManager();
        $file = $fileUploader->upload($request,"image");
        $name = $request->request->get('name');
        $categorie_id = $request->request->get('categorie_id');
        $description = $request->request->get('description');
        $color = $request->request->get('color');
        $categorie = $catigorieRepository->find($categorie_id);
        $theme = $em->getRepository(Theme::class)->find($id);
        $theme->setName($name ?? $theme->getName());
        $theme->setCategorie($categorie);
        $theme->setDescription($description ?? $theme->getDescription());
        $theme->setPhoto($file['photo']);
        $theme->setColor($color ?? $theme->getColor());
        $theme->setDateModif(new \DateTime());
        $em->persist($theme);
        $em->flush();

        $view = $this->view($theme, 200);
        return $this->handleView($view);
    }

    /**
     * @Route("/api/theme/delete/{id}",methods={"DELETE"})
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $theme = $entityManager->getRepository(Theme::class)->find($id);

        if (!$theme) {
            throw $this->createNotFoundException(
                'No Theme found for id ' . $id
            );
        }

        foreach ($theme->getQuestion() as $question) {


            foreach ($question->getReponse() as $reponse) {
                $entityManager->remove($reponse);
            }
            $entityManager->remove($question);
        }
        $entityManager->remove($theme);
        $entityManager->flush();
        $view = $this->view($theme, 200);
        return $this->handleView($view);
    }


}
