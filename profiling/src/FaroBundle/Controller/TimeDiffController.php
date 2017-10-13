<?php

namespace FaroBundle\Controller;

use Carbon\Carbon;
use FaroBundle\Classes\OpeningTimeConverter;
use FaroBundle\Classes\TimeDiff;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TimeDiffController extends Controller
{
    /**
     * @return Response
     * @Route("/timediff", name="timediff")
     * @Template("FaroBundle:TimeDiff:index.html.twig")
     */
    public function timeDiffAction(Request $request)
    {
        $opening_times = $request->get("opening_times");
        $from = Carbon::parse($request->get("from"));
        $to   = Carbon::parse($request->get("to"));

        $openingTimeConverter = new OpeningTimeConverter();
        $openingHours = $openingTimeConverter->convert($opening_times);

        $timeDiff = new TimeDiff($openingHours);

        $officeMinutes = $timeDiff->diffWithoutNonworkingHours($from, $to);

        return [
            'text' => $officeMinutes
        ];
    }

    /**
     * @return Response
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction() {
        return [
            'text' => "what"
        ];
    }
}
