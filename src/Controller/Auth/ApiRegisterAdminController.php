<?php

namespace App\Controller\Auth;


use App\Entity\User;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class ApiRegisterAdminController extends AbstractFOSRestController
{


    /**
     * @Route("/api/register/affich", name="Affiche_List_joureur",methods={"GET"})
     * @param Request $request
 */
    public function indexAction(Request $request): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findAll();
        $view = $this->view($user, 200);
        return $this->handleView($view);
    }

    /**
     * @Route("/api/register/edit/{id}",name="edit_User",methods={"POST"})
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        $role = $this->getDoctrine()->getManager();
        $Roles = $request->request->get('roles');
        $rol = explode(",", $Roles);
        $Roless = $role->getRepository(User::class)->find($id);
        $Roless->setRoles($rol ?? $Roless->getRoles());
        $Roless->setDateModif(new \DateTime());
        $role->persist($Roless);
        $role->flush();
        $view = $this->view($Roless, 200);
        return $this->handleView($view);
    }

    /**
     * @Route("/api/register/delete/{id}",methods={"DELETE"})
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            throw $this->createNotFoundException(
                'No Catégorie found for id ' . $id
            );
        }
        $entityManager->remove($user);
        $entityManager->flush();
        $view = $this->view($user, 200);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param Swift_Mailer $mailer
     * @param UserRepository $UserRepository
     * @param UserPasswordEncoderInterface $encoder
     * @param int $length
     * @return Response
     * @Route("api/password/reset", name="email", methods={"POST"})
     */
    public function SendMail(Request $request, Swift_Mailer $mailer, UserRepository $UserRepository, UserPasswordEncoderInterface $encoder, $length = 12)
    {

        $reset = $this->getDoctrine()->getManager();
        $email = $request->get('email', null);
        $user = $UserRepository->findOneBy(['email' => $email]);
        if ($user) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $new_password = '';
            for ($i = 0; $i < $length; $i++) {
                $new_password .= $characters[rand(0, $charactersLength - 1)];
            }
            $body = 'Votre nouveau mot de passe est : <b>' . $new_password . '</b><br> 
                 Pour le changer il suffit de se connecter, d\'aller à ton espace personnel et de choisir un nouveau';
            $message = (new Swift_Message('Réinsialisation du mot de passe'))
                ->setFrom(['Rabii12terres@gmail.com' => 'Rabii Terres'])
                ->setTo([$email])
                ->setBody($body)
                ->setContentType('text/html');
//                ->attach(\Swift_Attachment::fromPath(__DIR__ . '/public/images/burgerquiz.jpg'));
            $mailer->send($message);
            $user->setPassword($encoder->encodePassword($user, $new_password));
            $reset->persist($user);
            $reset->flush();
            $view = $this->view(null, Response::HTTP_OK);
            return $this->handleView($view);
        } else {
            $view = $this->view(null, Response::HTTP_NOT_FOUND);
            return $this->handleView($view);
        }
    }
//    /**
//     * @Route("/api/loginConnected/{id}", name="loginConnect1",methods={"POST"})
//     * @param UserRepository $userRepository
//     * @param Request $request
//     * @param int $id
//     * @return Response
//     */
//
//    public function getUserConnected(UserRepository $userRepository,
//                                     Request $request,
//                                     int $id
//    ): Response
//    {
//        $us = $this->getDoctrine()->getManager();
//
//        $user = $userRepository->findOneBy(['id' => $id]);
//
////        $Method=$request->getMethod();//dd($Method);
//        $connect = $request->request->getBoolean('connect');
////     dd($connect);
//        //var_dump($connect);
//        if (!$user) {
//
//            throw $this->createNotFoundException(
//                'No games found for id ' . $id
//            );
//        }
////        dd($connect);
//        if ($connect) {
//            $users = $us->getRepository(User::class)->find($id);
//            $users->setIsconnected(1);
//            $us->persist($users);
//            $us->flush();
//            $s = $users->getIsconnected();
//            return new JsonResponse('connect');
////            $view = $this->view($users, 200);
////            return $this->handleView($view);
//        } else {
//            $users = $us->getRepository(User::class)->find($id);
//            $users->setIsconnected(0);
//            $us->persist($users);
//            $us->flush();
//            $s = $users->getIsconnected();
//            return new JsonResponse('disconnect');
//
//        }


//    }

}
