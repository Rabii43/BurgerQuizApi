<?php

namespace App\Controller\Setting;

use App\Repository\OumaymaRepository;
use App\Repository\QuestionRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingTokenController extends AbstractFOSRestController
{

//    /**
//     * @Route("/api/add age", name="liste",methods={"POST"})
//     * @param Request $request
//     * @param OumaymaRepository $oumaymaRepository
//     * @return Response
//     */
//    public function aff( Request $request ,OumaymaRepository $oumaymaRepository): Response
//    {
//
//    }
        /**
     * @Route("/api/listTheme/affichAll", name="listeTheme",methods={"POST"})
     * @param QuestionRepository $questionRepository
     * @param Request $request
     * @return Response
     */
    public function affiche(QuestionRepository $questionRepository, Request $request): Response
    {
        $questions = $request->request->get('questions');
        $questionCherchTheme = explode(",", $questions);
        $theme = [];
        $questionCherchTheme = array_unique($questionCherchTheme);
        foreach ($questionCherchTheme as $questionCherchThem) {
            $themeInfo = $questionRepository->find(['id' => $questionCherchThem]);
            $theme[] = ($themeInfo->getTheme()->getPhoto());
        }
        $theme = array_unique($theme);
        $view = $this->view($theme, 200);
        return $this->handleView($view);
    }

    /**
     * @Route("api/setting/affichetoken",name="afficheToken",methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function afficheToken(Request $request): Response
    {
        $token = $request->request->get('token');
        $decodeToken = base64_decode($token);

        $view = $this->view($decodeToken, 200);
        return $this->handleView($view);
    }

    /**
     * @Route("api/setting/dcodeJwtoken",name="jwToken",methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function JwToken(Request $request): Response
    {
        $token = $request->get('token');

//        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MjMxNDM1MDUsImV4cCI6MTYyMzE0NzEwNSwicm9sZXMiOiJST0xFX0FETUlOLFJPTEVfVVNFUiIsInVzZXJuYW1lIjoiUmFiaWkgVGVycmVzIiwiaWQiOjcsImVtYWlsIjoicmFiaWkxMnRlcnJlc0BnbWFpbC5jb20iLCJhdmF0YXIiOiJodHRwczpcL1wvY2RuLnZ1ZXRpZnlqcy5jb21cL2ltYWdlc1wvbGlzdHNcLzIuanBnIn0.PPV_gnisco-ZIq4NEyq5vEzsNN2yTPpP31Vb91bcCqRCim0VkErjJ7WsibYX3fllNSy30N_pLZKFS0-4BpJKuOFCHp0TqqpFMDGnjt9ekuke6ijDWgpPkm4yKZ_twHHi7h3k1mM6bho8tMGnqmLFnP6V_USWQWY0LvBlftG4uM3qUb57jJVpgF6XlEWQrx50whoYj0Mt_rOJapXuCKpGWWKp4HBnkvpZAt4tSSoVNjruAbJVsDp_MI2_e21ZDnSaeIONjZfV5D8cckr_pd-ghWCDInfrdjo1nhiXYZAY_n4GMH08naYJaQiBwGFRD-6wgRU3rFD7Swf8-myn46BgkRV1ZO-zWHju86TKSV2_98wX6RmM6tCAPn7Lt1nqdLxaQtPpW5br-85PjopUxsRtwWxz62hjFZMnMzOc9uiGVcEhYR4yVEhodmJC3bmtXTBs2Lh7Xn7WlVGPqcA-MycokkOmBFp-6q3J6aK7hSdjoVosxj6_P8q5TtCfVYMPj_4b2FbG6JLnC-i5lxGhBeXof65-5jMzbK_hoLAw9fsGZLK4N_BBl8QUZ6K6LTFtDTETeHuIf8W6twKnaPGhWvkyuTdPSHya5AXcF6de1oYQltb1VTwRm0TqM97mWRy-vUX87h5y0t_GbjZ6S3I-MWu4jw-OvvmOKYxoGKaFQMMbr3w";
        $tokenParts = explode(".", $token);
        $tokenHeader = base64_decode($tokenParts[0]);
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtHeader = json_decode($tokenHeader);
        $jwtPayload = json_decode($tokenPayload);
        $array = ['id' => $jwtPayload->id, 'email' => $jwtPayload->email, 'username' => $jwtPayload->username, 'roles' => $jwtPayload->roles, 'avatar' => $jwtPayload->avatar];
        $view = $this->view($array, 200);
        return $this->handleView($view);
    }
}
