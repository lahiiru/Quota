<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
class ClientController extends Controller
{

    public function summaryAction(Request $request){
        $html=$this->render('dashboard/client/summary.html.twig', array(
            'x' => ''
        ));

        return $html;
    }

    public function requestsAction(Request $request){

    }

    public function usageAction(Request $request){

    }

    public function packagesAction(Request $request){

    }

    public function settingsAction(Request $request){

    }


}
