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

class InsertData
{
    private $em;
    private $id;
    private $controller;

    /**
     * FetchData constructor.
     * @param $em
     * @param $id
     */
    public function __construct($controller,$anonymous=false)
    {
        $this->controller=$controller;
        $this->em = $controller->getDoctrine()->getManager();
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
                return $slave->getSid();
        }
        return null;
    }

    public function activatePackage($package){
        $this->persist($package);
    }

    private function getSlave($mac,$zone){
        return $this->controller->getDoctrine()
                ->getRepository('AppBundle:slave_user')
                ->findOneByMac(['mac'=>$mac,'zone'=>$zone]);
    }

    public function updateUsage($mac,$zone,$kbytes){
        $slave=$this->getSlave($mac,$zone);
        $usage=new slave_usage($slave,new \DateTime('now'),$kbytes);
        $this->persist($usage);
    }
}