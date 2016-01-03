<?php
namespace AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\DTO;
use AppBundle\DQL;
use AppBundle\Entity;
class ClientController extends Controller
{
    public function summaryAction(Request $request)
    {
        $fetcher = new DQL\FetchData($this);
        $clientSummaryDTO = $fetcher->getClientSummaryDTO();
        $html=$this->render('dashboard/client/summary.html.twig', array(
            'arrayDTO'=>$clientSummaryDTO,
        ));
        return $html;
    }

    public function requestsAction(Request $request)
    {
        $fetcher = new DQL\FetchData($this);
        $newRequests = $fetcher->getRequests();
        if(empty($newRequests)) {
            $newRequests=[];
        }

        $requests = new \StdClass();
        $requests->new = [];
        $requests->change = [];
        $requests->message = [];
        $requests->offer = [];
        $requests->want = [];

        foreach ($newRequests as $request) {
            $type = $request['request']->getRequestData()['type'];
            array_push($requests->$type, $request);
        }

        $html = $this->render('dashboard/client/requests.html.twig', array(
            'requests' => $requests,
        ));

        return $html;
    }

    public function requestProcessAction(Request $request,$action){
        $fetcher = new DQL\FetchData($this);
        $id=$request->request->get('id');
        $newRequest=$fetcher->validateNewRequest($id);

        if($newRequest==null){
            return new Response('ERROR');; //implemet error
        }

        $inseter = new DQL\InsertData($this);
        $sid=$inseter->processNewUserRequest($newRequest,$action=="reject");
        if(""==$sid){
            return new Response("<p class=\"text-center\"><b>Successfully Rejected.</b></p>");
        }
        return new Response("<p class=\"text-center\"><b>Operation was successful.</b></b></p><p class=\"text-center\">Client id is $sid</p>");

    }

    public function usageAction(Request $request){

    }

    public function packagesAction(Request $request){

    }

    public function settingsAction(Request $request){

    }
}