<?php
/**
 * Created by PhpStorm.
 * User: Lahiru
 * Date: 02/01/2016
 * Time: 11:20 PM
 */

namespace AppBundle\DQL;


use AppBundle\Entity\slave_usage;
use AppBundle\Entity\slave_user;
use Proxies\__CG__\AppBundle\Entity\slave_request;
use Symfony\Component\Config\Definition\Exception\Exception;

class InsertData
{
    private $em;
    private $id;
    private $controller;
    private $isAnonymous;
    /**
     * InsertData constructor.
     * @param $em
     * @param $id
     */
    public function __construct($controller,$anonymous=false)
    {
        $this->controller=$controller;
        $this->em = $controller->getDoctrine()->getManager();
        $this->isAnonymous = $anonymous;
        if(!$anonymous) {
            $cUser = $controller->get('security.token_storage')->getToken()->getUser();
            $this->id = $cUser->getId();
        }
    }

    private function persist($object){
        $this->em->persist($object);
        $this->em->flush();
    }

    private function remove($object){
        $this->em->remove($object);
        $this->em->flush();
    }

    public function processRequest($request,$reject=false){
        if($reject){
            $this->remove($request);
            return ".";
        }
        $data=$request->getRequestData();
        $type=$data['type'];
        $cUser=$this->controller->get('security.token_storage')->getToken()->getUser();
        switch($type){
            case 'new':
                $newSlave=new slave_user($data['mac'],$cUser,$data['name'],0,$data['package']);
                $this->persist($newSlave);
                $this->remove($request);
                return $newSlave->getSid();
            case 'message':
                $this->remove($request);
                return null;
            case 'change':
                $newPackage=$data['package'];
                $slave=$request->getSlaveUser();
                $slave->setPackage($newPackage);
                $this->persist($slave);
                $this->remove($request);
                return $slave->getSid();
        }
        return null;
    }

    public function updateClientPackage($sid,$zone,$package){
        $su=$this->getSlaveBySid($sid,$zone);
        if($su==null){
            return null;
        }
        $su->setPackage($package);
        $this->persist($su);
        return true;
    }

    public function activatePackage($package){
        $this->persist($package);
    }

    private function getSlave($mac,$zone){
        $fetcher=new FetchData($this->controller,$this->isAnonymous);
        return $fetcher->getClientByMac($mac,$zone);
    }

    private function getSlaveBySid($sid,$zone){
        $fetcher=new FetchData($this->controller,$this->isAnonymous);
        return $fetcher->getClientBySid($sid,$zone);
    }

    public function updateUsage($mac,$zone,$kbytes){
        $slave=$this->getSlave($mac,$zone);
        $usage=new slave_usage($slave,new \DateTime('now'),$kbytes);
        $this->persist($usage);
    }

    public function addChangeRequest($mac,$zone,$package){
        $slave = $this->getSlave($mac,$zone);
        $request = new slave_request();
        if($slave==null){
            return false;
        }
        try{
            $request->setSlaveUser($slave);
            $request->setPending(1);
            $request->setDate(new \DateTime('now'));
            $data =
                [
                    "type"=>"change",
                    "package"=>"$package"
                ];
            $request->setRequestData($data);
            $this->persist($request);
            return true;
        }
        catch(Exception $e){
            return false;
        }
    }

    public function addMessageRequest($mac,$zone,$subject,$body){
        $slave = $this->getSlave($mac,$zone);
        $request = new slave_request();
        if($slave==null){
            return false;
        }
        try{
            $request->setSlaveUser($slave);
            $request->setPending(1);
            $request->setDate(new \DateTime('now'));
            $data =
                [
                    "type"=>"message",
                    "subject"=>"$subject",
                    "body"=>"$body"
                ];
            $request->setRequestData($data);
            $this->persist($request);
            return true;
        }
        catch(Exception $e){
            return false;
        }
    }

    public function addNewRequest($zone,$mac,$name,$package,$msg){
        $slave = $this->getSlave("FFFFFFFFFFFF",$zone);
        $request = new slave_request();
        if($slave==null){
            return false;
        }
        try{
            $request->setSlaveUser($slave);
            $request->setPending(1);
            $request->setDate(new \DateTime('now'));
            $data =
                [
                    "type"=>"new",
                    "mac"=>"$mac",
                    "name"=>"$name",
                    "package"=>"$package",
                    "message"=>"$msg"
                ];
            $request->setRequestData($data);
            $this->persist($request);
            return true;
        }
        catch(Exception $e){
            return false;
        }
    }
}