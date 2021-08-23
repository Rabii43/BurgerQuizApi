<?php

namespace App\Controller\GestionCatégorie;

use App\Entity\Categorie;
use App\Service\FileUploader;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @method transformJsonBody($request)
 */
class ApiCategorieController extends AbstractFOSRestController
{
    /**
     * @Route("/api/categorie/affichall", name="Affiche_List_Categorie",methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        $view = $this->view($categories, 200);
        return $this->handleView($view);
    }

    /**
     * @Route("/api/categorie/affich/{id}", name="Categorie_show")
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $Categorie = $this->getDoctrine()->getRepository(Categorie::class)->find($id);
        if (!$Categorie) {
            throw $this->createNotFoundException(
                'No Catégorie found for id ' . $id
            );
        }

        $view = $this->view($Categorie, 200);
        return $this->handleView($view);
    }
    /**
     * @Route("/api/categorie/add", name="insert_Categorie2588",methods={"POST"})
     * @param Request $request
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function add(Request $request, FileUploader $fileUploader): Response
    {
        $em = $this->getDoctrine()->getManager();
        $name_categorie = $request->request->get('name_categorie');
        $file = $fileUploader->upload($request,"image");
        $Categorie = new Categorie();
        $Categorie->setNameCategorie($name_categorie);
        $Categorie->setPhotoCategorie($file['photo_categorie']);
        $Categorie->setDateCreation(new \DateTime());
        $Categorie->setDateModif(new \DateTime());
        $em->persist($Categorie);
        $em->flush();
        $view = $this->view($Categorie, 200);
        return $this->handleView($view);
    }

    /**
     * @Route("/api/categorie/edit/{id}",name="edit_Categorie",methods={"POST"})
     * @param Request $request
     * @param int $id
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function update(Request $request, int $id,FileUploader $fileUploader): Response
    {
        $em = $this->getDoctrine()->getManager();
        $file = $fileUploader->upload($request,"image");
        $name = $request->request->get('name_categorie');
        $Categorie = $em->getRepository(Categorie::class)->find($id);
        $Categorie->setNameCategorie($name ?? $Categorie->getNameCategorie());
        $Categorie->setPhotoCategorie($file['photo_categorie']);
        $Categorie->setDateModif(new \DateTime());
        $em->persist($Categorie);
        $em->flush();
        $view = $this->view($Categorie, 200);
        return $this->handleView($view);
    }

    /**
     * @Route("/api/categorie/delete/{id}",methods={"DELETE"})
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $categorie = $entityManager->getRepository(Categorie::class)->find($id);

        if (!$categorie) {
            throw $this->createNotFoundException(
                'No Catégorie found for id ' . $id
            );
        }
        foreach ($categorie->getTheme() as $theme) {
            foreach ($theme->getQuestion() as $question) {
                foreach ($question->getReponse() as $reponse) {
                    $entityManager->remove($reponse);
                }
                $entityManager->remove($question);
            }
            $entityManager->remove($theme);
        }
        $entityManager->remove($categorie);
        $entityManager->flush();
        $view = $this->view($categorie, 200);
        return $this->handleView($view);
    }

}
