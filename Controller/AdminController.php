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
        $tokenParameter = $preferencesService->ExternalLoginTokenParameter;
        $redirectUrl = $preferencesService->ExternalLoginRedirectUrl;
        $tokenUrl = $preferencesService->ExternalLoginTokenUrl;
        $caPath = $preferencesService->ExternalLoginCAPath;
        $certFile = $preferencesService->ExternalLoginCertFile;
        $keyFile = $preferencesService->ExternalLoginKeyFile;

        if ($request->isMethod('POST')) {
            $tokenParameter = $request->request->get('token-parameter');
            $redirectUrl = $request->request->get('redirect-url');
            $tokenUrl = $request->request->get('token-url');
            $caPath = $request->request->get('ca-path');
            $certFile = $request->request->get('cert-file');
            $keyFile = $request->request->get('key-file');
            $preferencesService->set('ExternalLoginTokenParameter', $tokenParameter);
            $preferencesService->set('ExternalLoginRedirectUrl', $redirectUrl);
            $preferencesService->set('ExternalLoginTokenUrl', $tokenUrl);
            $preferencesService->set('ExternalLoginCAPath', $caPath);
            $preferencesService->set('ExternalLoginCertFile', $certFile);
            $preferencesService->set('ExternalLoginKeyFile', $keyFile);
            $em->flush();
        }
        return array(
            'tokenParameter' => $tokenParameter,
            'redirectUrl' => $redirectUrl,
            'tokenUrl' => $tokenUrl,
            'caPath' => $caPath,
            'certFile' => $certFile,
            'keyFile' => $keyFile,
            'callbackUrl' => $baseurl . '/external_login'
        ); 

    }
}
