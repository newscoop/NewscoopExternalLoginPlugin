<?php

namespace Newscoop\ExternalLoginPluginBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdminController extends Controller
{
    /**
     * @Route("/admin/externallogin")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->container->get('em');
        $baseurl = $request->getScheme() . '://' . $request->getHttpHost();
        $preferencesService = $this->container->get('system_preferences_service');
        //'https://login.sourcefabric.org/server/simple/login/';
        $redirectUrl = $preferencesService->ExternalLoginRedirectUrl; 
        //'openid_identity';
        $tokenParameter = $preferencesService->ExternalLoginTokenParameter;

        if ($request->isMethod('POST')) {
            $redirectUrl = $request->request->get('redirect-url');
            $tokenParameter = $request->request->get('token-parameter');
            $preferencesService->set('ExternalLoginRedirectUrl', $redirectUrl);
            $preferencesService->set('ExternalLoginTokenParameter', $tokenParameter);
            $em->flush();
        }
        return array(
            'redirectUrl' => $redirectUrl,
            'tokenParameter' => $tokenParameter,
            'callbackUrl' => $baseurl . '/external_login'
        ); 

    }
}
