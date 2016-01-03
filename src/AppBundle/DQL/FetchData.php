<?php

/**
 * Created by PhpStorm.
 * User: Lahiru
 * Date: 01/01/2016
 * Time: 8:30 PM
 */
namespace AppBundle\DQL;

class FetchData
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

    private function fetchResult($dql,$firstElem=false){
        $query = $this->em->createQuery($dql);
        $result=$query->getResult();
        if(empty($result)){
            return null;
        }
        if($firstElem){
            return $result[0];
        }
        return $result;
    }

    public function getRunningDataPackage(){
        return $this->fetchResult("SELECT p FROM AppBundle\Entity\data_package p WHERE p.start < CURRENT_TIMESTAMP() AND CURRENT_TIMESTAMP() < p.end AND p.auth_user=$this->id ORDER BY p.pid DESC",true);
    }

    public function getClientSummaryDTO(){
        return $this->fetchResult("SELECT NEW AppBundle\DTO\ClientSummaryDTO(su.sid,su.name,su.mac,su.state,su.package,SUM(u.kbytes)) FROM AppBundle\Entity\slave_usage u JOIN u.slave_user su JOIN su.auth_user au WHERE au.id=$this->id GROUP BY su");
    }

    public function getClientStatusDTO($runningPackage){
        $query = $this->em->createQuery("SELECT NEW AppBundle\DTO\ClientStatusDTO(su.name,MAX(u.date),SUM(u.kbytes),su.state) FROM AppBundle\Entity\slave_usage u JOIN u.slave_user as su JOIN su.auth_user as au WHERE au=$this->id AND  :st < u.date AND u.date < :end GROUP BY su.sid");
        $pstart=$runningPackage->getStart();
        $pend=$runningPackage->getEnd();
        $query->setParameter('st', $pstart)
            ->setParameter('end', $pend);
        $result = $query->getResult();
        if(empty($result)){
            return null;
        }
        return $result;
    }

    public function getTotalUsageObj($runningPackage){
        $query = $this->em->createQuery("SELECT au.username as authUser ,MAX(u.date) as lastUpdate,su.name,SUM(u.kbytes) as total FROM AppBundle\Entity\slave_usage u JOIN u.slave_user as su JOIN su.auth_user as au WHERE au=$this->id AND  :st < u.date AND u.date < :end GROUP BY au.id");
        $pstart=$runningPackage->getStart();
        $pend=$runningPackage->getEnd();
        $query->setParameter('st', $pstart)
            ->setParameter('end', $pend);
        $result = $query->getResult();
        if(empty($result)){
            return null;
        }
        return $result[0];
    }

    public function getRequests($pending=1){
        return $this->fetchResult("SELECT sr as request,su as slave_user FROM AppBundle\Entity\slave_request sr JOIN sr.slave_user su JOIN su.auth_user au WHERE au.id=$this->id AND sr.pending=$pending");
    }

    public function validateNewRequest($requestId){ // return null if auth_user doesn't match
        $result=$this->fetchResult("SELECT sr FROM AppBundle\Entity\slave_request sr JOIN sr.slave_user su JOIN su.auth_user au WHERE au.id=$this->id AND sr.request_id=$requestId");
        if(empty($result)){
            return null;
        }
        else{
            return $result[0];
        }
    }

}