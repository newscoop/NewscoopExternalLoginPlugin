<?php

namespace Newscoop\ExternalLoginPluginBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response; 

/**
 * Route("/external_login")
 */ 
class ExternalLoginController extends Controller
{
    /**
     * @Route("/external_login")
     * @Route("/external_login/")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->container->get('em');
        $name = 'external login page';
        $error = array('message' => 'no errors');
        $languages = $em->getRepository('Newscoop\Entity\Language')->getLanguages();

        // TODO: check for token
        // if token does not exist, redirect to sso login
        // if token exists, login user with selected language

        if ($request->isMethod('POST')) {
            $this->checkToken($request);
        }

        return array(
            'name' => $name,
            'error' => $error,
            'defaultLanguage'   => $this->getDefaultLanguage($request, $languages),
            'languages' => $languages
        );
    }

    private function checkToken($request) {
        $language = $request->request->get('login_language');
        //error_log(print_r($request, true));
        error_log($language);
    }

    private function getDefaultLanguage($request, $languages)
    {
        $defaultLanguage = 'en';

        if ($request->request->has('TOL_Language')) {
            $defaultLanguage = $request->request->get('TOL_Language');
        } elseif ($request->cookies->has('TOL_Language')) {
            $defaultLanguage = $request->cookies->get('TOL_Language');
        } else {
            // Get the browser languages
            $browserLanguageStr = $request->server->get('HTTP_ACCEPT_LANGUAGE', '');
            $browserLanguageArray = preg_split("/[,;]/", $browserLanguageStr);
            $browserLanguagePrefs = array();
            foreach ($browserLanguageArray as $tmpLang) {
                if (!(substr($tmpLang, 0, 2) == 'q=')) {
                    $browserLanguagePrefs[] = $tmpLang;
                }
            }
            // Try to match preference exactly.
            foreach ($browserLanguagePrefs as $pref) {
                if (array_key_exists($pref, $languages)) {
                    $defaultLanguage = $pref;
                    break;
                }
            }
            // Try to match two-letter language code.
            if (is_null($defaultLanguage)) {
                foreach ($browserLanguagePrefs as $pref) {
                    if (substr($pref, 0, 2) != "" && array_key_exists(substr($pref, 0, 2), $languages)) {
                        $defaultLanguage = $pref;
                        break;
                    }
                }
            }

            // HACK: the function regGS() strips off the ":en" from
            // english language strings, but only if it knows that
            // the language being displayed is english...and it knows
            // via the cookie.
            $request->request->set('TOL_Language', $defaultLanguage);
            $request->cookies->set('TOL_Language', $defaultLanguage);
        }

        return $defaultLanguage;
    }
}
