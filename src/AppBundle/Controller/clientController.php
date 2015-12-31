<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\DTO;
use AppBundle\Entity;

class ClientController extends Controller
{

    public function summaryAction(Request $request){
        $cUser=$this->get('security.token_storage')->getToken()->getUser();
        $cId=$cUser->getId();
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery("SELECT p FROM AppBundle\Entity\data_package p WHERE p.start < CURRENT_TIMESTAMP() AND CURRENT_TIMESTAMP() < p.end AND p.auth_user=$cId");

        $cPackage = $query->getResult()[0];
        $pstart=$cPackage->getStart();
        $pend=$cPackage->getEnd();

        $query = $em->createQuery("SELECT NEW AppBundle\DTO\ClientSummaryDTO(su.sid,su.name,su.mac,su.state,su.package,SUM(u.kbytes)) FROM AppBundle\Entity\slave_usage u JOIN u.slave_user su JOIN su.auth_user au WHERE au.id=$cId GROUP BY su");
        $clientSummaryDTO = $query->getResult();
        //var_dump($clientSummaryDTO);

        $html=$this->render('dashboard/client/summary.html.twig', array(
            'arrayDTO'=>$clientSummaryDTO,
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
