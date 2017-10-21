<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TestController extends Controller {

    /**
     * @Route("/")
     * @Template()
     */
    public function luckyNumberAction()
    {
        $number = mt_rand(0, 100);

        return [
            'number' => $number
        ];
    }
}