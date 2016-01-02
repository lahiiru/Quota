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

        $requests = new \StdClass();
        $requests->new=[];
        $requests->change=[];
        $requests->message=[];
        $requests->offer=[];
        $requests->want=[];

        foreach($newRequests as $request)
        {
            $type=$request['request']->getRequestData()['type'];
            array_push($requests->$type,$request);
        }

        var_dump($requests);
        $html=$this->render('dashboard/client/requests.html.twig', array(
            'requests'=>$requests,
        ));

        return $html;
    }

    public function requestAcceptAction(Request $request){
        $fetcher = new DQL\FetchData($this);
        $id=$request->request->get('id');
        $newRequest=$fetcher->validateNewRequest($id);

        if($newRequest==null){
            return ""; //implemet error
        }

        $inseter = new DQL\InsertData($this);
        $inseter->processNewUserRequest($newRequest);

        //var_dump($id);

    }

    public function usageAction(Request $request){
    }

    public function packagesAction(Request $request){
    }

    public function settingsAction(Request $request){
    }
}