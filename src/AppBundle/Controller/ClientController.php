<?php
namespace AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\DTO;
use AppBundle\DQL;
use AppBundle\Entity;
class ClientController extends Controller
{
    public function summaryAction(Request $request){

        $fetcher = new DQL\FetchData($this);

        $cPackage = $fetcher->getRunningDataPackage();

        $pstart=$cPackage->getStart();
        $pend=$cPackage->getEnd();

        $clientSummaryDTO = $fetcher->getClientSummaryDTO();

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