<?php

namespace App\Controller\Setting;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class profil extends AbstractFOSRestController
{


    /**
     * @Route("/api/loginConnected/{id}", name="loginConnecte",methods={"POST"})
     * @param Request $request
     * @param int $id
     * @param UserRepository $userRepository
     * @return Response
     */

    public function UserConnected(Request $request, int $id, UserRepository $userRepository): Response

    {
        $us = $this->getDoctrine()->getManager();
        $user = $userRepository->findOneBy(['id' => $id]);

        $connect = $request->request->getBoolean('connect');

        var_dump($connect);
        if ($connect) {
            $conn = 1;
            $user->setIsconnected($conn);
            $us->persist($user);
            $us->flush();
            $array = ['username' => $user->getUsername(), 'roles' => $user->getRoles(), 'avatar' => $user->getAvatar(), 'statue' => $user->getIsconnected()];
        } else {
            $conn = 0;
            $user->setIsconnected($conn);
            $us->persist($user);
            $us->flush();
            $array = ['username' => $user->getUsername(), 'roles' => $user->getRoles(), 'avatar' => $user->getAvatar(), 'statue' => $user->getIsconnected()];
        }

        $view = $this->view($array, 200);
        return $this->handleView($view);
    }





    /**
     * @Route("/api/profil/edit/{id}",name="edit_profil",methods={"POST"})
     * @param Request $request
     * @param int $id
     * @param FileUploader $fileUploader
     * @param UserRepository $userRepository
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function update(Request $request, int $id, FileUploader $fileUploader, UserRepository $userRepository, UserPasswordEncoderInterface $encoder): Response
    {
        $em = $this->getDoctrine()->getManager();
        $file = $fileUploader->upload($request, "image");
        $name = $request->request->get('username');
        $email = $request->request->get('email');

        $user = $userRepository->findOneBy(['id' => $id]);
        if ($user) {
            $user->setUsername($name ?? $user->getUsername());
            $user->setEmail($email ?? $user->getEmail());
            $user->setAvatar($file['photo'] ?? $user->getAvatar());
            $user->setDateModif(new \DateTime());
            $em->persist($user);
            $em->flush();
        }
        $array = ['id' => $user->getId(), 'email' => $user->getEmail(), 'username' => $user->getUsername(), 'roles' => $user->getRoles(), 'avatar' => $user->getAvatar()];

        $view = $this->view($array, 200);
        return $this->handleView($view);
    }

    /**
     * @Route("/api/profil/editPassword/{id}",name="edit_Password",methods={"POST"})
     * @param Request $request
     * @param int $id
     * @param UserRepository $userRepository
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     */
    public function updatePassword(Request $request, int $id, UserRepository $userRepository, UserPasswordEncoderInterface $encoder): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        $password = $request->request->get('lastpassword');

        $newpassword = $request->request->get('newpassword');
        $user = $userRepository->findOneBy(['id' => $id]);
        $password = $encoder->isPasswordValid($user, $password);

        if($password){
        $newpassword = $encoder->encodePassword($user, $newpassword);
            $user->setPassword($newpassword);
            $user->setDateModif(new \DateTime());
            $em->persist($user);
            $em->flush();
            $array = ['id' => $user->getId(),'email' => $user->getEmail(), 'username' => $user->getUsername(), 'roles' => $user->getRoles(),'avatar' => $user->getAvatar()];
            return new JsonResponse('password created');
        }else


        return new JsonResponse('password invalid');
    }

}
