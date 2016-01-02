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

    public function processNewUserRequest($request){
        $data=$request->getRequestData();
        $cUser=$this->controller->get('security.token_storage')->getToken()->getUser();
        $newSlave=new slave_user($data['mac'],$cUser,$data['name'],0,$data['package']);
        $this->em->persist($newSlave);
        $this->em->remove($request);
        $this->em->flush();
        return $newSlave->getSid();
    }
}