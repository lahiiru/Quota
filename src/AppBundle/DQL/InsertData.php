<?php
/**
 * Created by PhpStorm.
 * User: Lahiru
 * Date: 02/01/2016
 * Time: 11:20 PM
 */

namespace AppBundle\DQL;


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
    public function __construct($controller)
    {
        $this->controller=$controller;
        $cUser=$controller->get('security.token_storage')->getToken()->getUser();
        $this->em = $controller->getDoctrine()->getManager();
        $this->id = $cUser->getId();
    }

    private function persist($object){
        $this->em->persist($object);
        $this->em->flush();
    }

    private function remove($object){
        $this->em->remove($object);
        $this->em->flush();
    }

    public function processNewUserRequest($request,$reject=false){
        if($reject){
            $this->remove($request);
            return "";
        }
        $data=$request->getRequestData();
        $cUser=$this->controller->get('security.token_storage')->getToken()->getUser();
        $newSlave=new slave_user($data['mac'],$cUser,$data['name'],0,$data['package']);
        $this->persist($newSlave);
        $this->remove($request);
        return $newSlave->getSid();
    }

    public function activatePackage($package){
        $this->persist($package);
    }
}