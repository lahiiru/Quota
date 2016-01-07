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
        $responseObj->status="NEW";
        try {
            $fetcher = new DQL\FetchData($this,true);

            $result = $fetcher->getClientStatus($mac,$zone);

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
            return new Response(str_replace('"',"'",json_encode($responseObj)));
        }
    }
    public function usageUpdateAction(Request $request,$zone,$mac,$kbytes){
        $responseObj = new \StdClass();
        try {

            $inserter = new DQL\InsertData($this,true);
            $fetcher = new DQL\FetchData($this,true);

            $result = $fetcher->getClientStatus($mac,$zone);
            $responseObj->status="NEW";
            if (!empty($result)) {
                switch ($result['state']) {
                    case 0:
                        $responseObj->status="OK";
                        break;
                    case 1:
                        $responseObj->status='OK';
                        break;
                    case 2:
                        $responseObj->status='OK';
                        break;
                }
                if($kbytes<=0){
                    $responseObj->status='INVALID';
                }else{
                    $inserter->updateUsage($mac,$zone,$kbytes);
                }
            }
        }
        catch(Exception $e){
            $responseObj->status='ERROR';
        }finally{
            return new Response(str_replace('"',"'",json_encode($responseObj)));
        }

    }

}