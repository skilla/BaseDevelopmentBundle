<?php

namespace Skilla\BaseDevelopmentBundle\Controller;

use \Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use \Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DefaultController
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @return JsonResponse
     */
    public function indexAction()
    {
        return $this->json('Homepage');
    }
}
