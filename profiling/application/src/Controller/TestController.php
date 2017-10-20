<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class TestController extends Controller {

    /**
     * @Route("/lucky")
     * @Template()
     */
    public function luckyAction()
    {
        $number = mt_rand(0, 100);

        return [
            'number' => $number
        ];
    }
}