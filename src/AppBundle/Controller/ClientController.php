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
            return new Response('ERROR'); //implemet error
        }

        $inseter = new DQL\InsertData($this);
        $sid=$inseter->processRequest($newRequest,$action=="reject");
        if($sid==null){
            return new Response("<p class=\"text-center\"><b>Unknown response.</b></p>");
        }
        elseif("."==$sid){
            return new Response("<p class=\"text-center\"><b>Successfully Rejected.</b></p>");
        }
        return new Response("<p class=\"text-center\"><b>Operation was successful.</b></b></p><p class=\"text-center\">Client id is $sid</p>");

    }

    public function usageAction(Request $request){

    }

    public function packagesAction(Request $request){
        $fetcher=new DQL\FetchData($this);
        $package=$fetcher->getRunningDataPackage()->getKbytes()/1000000;

        $srt='[0';
        $tempArray=[0];
        $num=$package;
        $x=0.01;
        $add=0.04;
        while($num>=$x){
            $srt.=','.$x;
            array_push($tempArray,$x);
            if($x==0.01)$add=0.04;
            if($x==0.05)$add=0.05;
            if($x==0.1)$add=0.4;
            if($x>=0.5){
                $add=0.5;
            }
            if($x>=5){
                $add=1;
            }
            $x=$x+$add;
        }
        $srt.=']';

        $packages=$fetcher->getPackageDetail();
        $mappedPackageArray=[];
        foreach($packages as $a){
            array_push($mappedPackageArray,
                [
                    'sid' => $a['sid'],
                    'name' => $a['name'],
                    'package' => $this->getClosestIndex($a['package']/1000000,$tempArray)
                ]
            );
        }
        $html = $this->render('dashboard/client/packages.html.twig', array(
            'range' => $srt,
            'slavePackages' => $mappedPackageArray,
            'total' => $package
        ));

        return $html;
    }

    public function packageChangeAction(Request $request){
        $fetcher=new DQL\FetchData($this);
        $json=$request->request->get("data");
        $packages=json_decode($json);
        $sum = array_reduce($packages, function($i, $obj)
        {
            return $i += $obj->package;
        });
        $total=$fetcher->getRunningDataPackage()->getKbytes()/1000000;
        if($total<$sum){
            return new Response("<p><b>Error</b></p><p>You can't exceed master package limit.</p>" );
        }
        $inserter=new DQL\InsertData($this);
        $user=$this->get('security.token_storage')->getToken()->getUser();
        $zone=$user->getZone();
        foreach($packages as $p){
            $inserter->updateClientPackage($p->sid,$zone,$p->package*1000000);
        }
        return new Response("<p><b>Success</b></p><p>Slave packages are updated.</p>");
    }

    private function getClosestIndex($search, $arr) {
        $closest = null;
        foreach ($arr as $item) {
            if ($closest === null || abs($search - $closest) > abs($item - $search)) {
                $closest = $item;
            }
        }
        return array_search($closest, $arr);
    }

    public function settingsAction(Request $request){

    }
}