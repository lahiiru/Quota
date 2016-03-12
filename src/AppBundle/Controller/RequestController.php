<?php
/**
 * Created by PhpStorm.
 * User: Lahiru
 * Date: 04/01/2016
 * Time: 10:33 AM
 */

namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\DQL;

class RequestController extends Controller
{
    /**
     * Process client request sent by app to check user status
     */

    public function userCheckAction(Request $request,$zone,$mac){
        $responseObj = new \StdClass();
        try {
            $fetcher = new DQL\FetchData($this,true);

            $result = $fetcher->getClientStatus($mac,$zone);

            $cPackage = $fetcher->getRunningDataPackageByZone($zone);
            $shared = $fetcher->getSharedQuotaByZone($zone);
            $remainingBytes = $cPackage->getKbytes()-$shared; // Max possible package by users request time.

            $responseObj->status="NEW";
            $responseObj->details=$remainingBytes;
            if (!empty($result)) {
                switch ($result['state']) {
                    case 0:
                        $responseObj->status="OK";

                        if($fetcher->isOver($mac,$zone)){
                            $responseObj->status="OVER";
                        }
                        break;
                    case 1:
                        $responseObj->status='BLOCKED';
                        break;
                }
                $responseObj->details=$fetcher->getClientResponse($mac,$zone);
            }
        }
        catch(Exception $e){
            $responseObj->status="ERROR";
        }
        finally{
            return new Response(str_replace("'","\"",json_encode($responseObj)));
        }
    }
    public function usageUpdateAction(Request $request,$zone,$mac,$kbytes){
        $responseObj = new \StdClass();
        try {
            $inserter = new DQL\InsertData($this,true);
            $fetcher = new DQL\FetchData($this,true);

            $result = $fetcher->getClientStatus($mac,$zone);

            $cPackage = $fetcher->getRunningDataPackageByZone($zone);
            $shared = $fetcher->getSharedQuotaByZone($zone);

            $remainingBytes = $cPackage->getKbytes()-$shared; // Max possible package by users request time.
            $responseObj->status="NEW";
            $responseObj->details=$remainingBytes;
            if (!empty($result)) {
                switch ($result['state']) {
                    case 0:
                        $responseObj->status="OK";
                        if($fetcher->isOver($mac,$zone)){
                            $responseObj->status="OVER";
                        }
                        break;
                    case 1:
                        $responseObj->status='BLOCKED';
                        break;
                }
                if($kbytes<=0){
                    $responseObj->status='INVALID';
                }else{
                    $inserter->updateUsage($mac,$zone,$kbytes);
                }
                $responseObj->details=$fetcher->getClientResponse($mac,$zone);
            }
        }
        catch(Exception $e){
            $responseObj->status='ERROR';
        }finally{
            return new Response(str_replace("'","\"",json_encode($responseObj)));
        }
    }

    public function changeAction(Request $request,$zone,$mac,$package){
        if($package<1000){
            return new Response("INVALID");
        }
        $inserter = new DQL\InsertData($this,true);
        if($inserter->addChangeRequest($mac,$zone,$package)){
            return new Response("OK");
        }
        else{
            return new Response("ERROR");
        }
    }

    public function messageAction(Request $request,$zone,$mac,$subject,$body){
        if(strlen($subject)<1 || strlen($body)<1){
            return new Response("INVALID");
        }

        $inserter = new DQL\InsertData($this,true);
        if($inserter->addMessageRequest($mac,$zone,$subject,$body)){
            return new Response("OK");
        }
        else{
            return new Response("ERROR");
        }
    }

    public function newAction(Request $request,$zone,$mac,$name,$package,$msg){
        if(strlen($name)<2 || strlen($package)<4){
            return new Response("INVALID");
        }

        $inserter = new DQL\InsertData($this,true);
        if($inserter->addNewRequest($zone,$mac,$name,$package,$msg)){
            return new Response("OK");
        }
        else{
            return new Response("ERROR");
        }
    }

}