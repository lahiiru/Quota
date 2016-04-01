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
    private $cuser;

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
            $this->cuser=$cUser;
            $this->id = $cUser->getId();
        }
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

    public function getRunningDataPackageByZone($zone){
        return $this->fetchResult("SELECT p FROM AppBundle\Entity\data_package p JOIN p.auth_user au WHERE p.start < CURRENT_TIMESTAMP() AND CURRENT_TIMESTAMP() < p.end AND au.zone='$zone' ORDER BY p.pid DESC",true);
    }

    public function getRunningUsageType($zone,$mac){
        $usageByType=$this->fetchResult("SELECT ut.id utid,ut.name utname,(su.package-SUM(u.kbytes)) remaining,SUM(u.kbytes) usage FROM AppBundle\Entity\slave_usage u JOIN u.usage_type ut JOIN u.slave_user su JOIN su.auth_user au WHERE au.zone='$zone' AND su.mac='$mac' AND ut.start < CURRENT_TIME() AND CURRENT_TIME() < ut.end GROUP BY ut HAVING remaining>1000 ORDER BY ut.precedence",true);
        if(empty($usageByType)){
            $usageByType=$this->fetchResult("SELECT ut.id utid,'OVER' utname,(su.package-SUM(u.kbytes)) remaining,SUM(u.kbytes) usage FROM AppBundle\Entity\slave_usage u JOIN u.usage_type ut JOIN u.slave_user su JOIN su.auth_user au WHERE au.zone='$zone' AND su.mac='$mac' AND ut.start < CURRENT_TIME() AND CURRENT_TIME() < ut.end GROUP BY ut ORDER BY ut.precedence",true);
        }
        //var_dump($usageByType);
        return $usageByType;
    }

    public function getClientSummaryDTO(){
		$cp = $this->getRunningDataPackage();
        //ut temporal hardcoded fixture
        return $this->fetchResult("SELECT NEW AppBundle\DTO\ClientSummaryDTO(su.sid,su.name,su.mac,su.state,su.package,SUM(u.kbytes)) FROM AppBundle\Entity\slave_usage u JOIN u.usage_type ut JOIN u.slave_user su JOIN su.auth_user au WHERE au.id=$this->id AND ut.id=1 GROUP BY su");
    }

    public function test(){
        return $this->fetchResult("SELECT au FROM AppBundle\Entity\slave_user AS su JOIN su.auth_user au");
    }

    public function getClientStatusDTO($runningPackage){
        //ut temporal hardcoded fixture
        $query = $this->em->createQuery("SELECT NEW AppBundle\DTO\ClientStatusDTO(su.name,MAX(u.date),SUM(u.kbytes),su.state) FROM AppBundle\Entity\slave_usage u JOIN u.usage_type ut JOIN u.slave_user as su JOIN su.auth_user as au WHERE ut.id=1 AND au=$this->id AND  :st < u.date AND u.date < :end GROUP BY su.sid");
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
        //ut temporal hardcoded fixture
        $query = $this->em->createQuery("SELECT au.username as authUser ,MAX(u.date) as lastUpdate,su.name,SUM(u.kbytes) as total FROM AppBundle\Entity\slave_usage u JOIN u.usage_type ut JOIN u.slave_user as su JOIN su.auth_user as au WHERE ut.id=1 AND au=$this->id AND  :st < u.date AND u.date < :end GROUP BY au.id");
        $pstart=$runningPackage->getStart();
        $pend=$runningPackage->getEnd();
        var_dump($pstart,$pend);
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

    public function getClientStatus($mac,$zone){
        $result=$this->fetchResult("SELECT su.state FROM AppBundle\Entity\slave_user su JOIN su.auth_user au WHERE su.mac='$mac' AND au.zone='$zone'",true);
        return $result;
    }

    public function isOver($mac,$zone){
        $result=$this->fetchResult("SELECT su.name,su.package,ut.id utid,ut.name utname,(su.package-SUM(u.kbytes)) remaining,SUM(u.kbytes) usage,su.comment,su.banner_url,au.pkey,au.skey FROM AppBundle\Entity\slave_usage u JOIN u.usage_type ut JOIN u.slave_user su JOIN su.auth_user au WHERE au.zone='$zone' AND su.mac='$mac' AND ut.start < CURRENT_TIME() AND CURRENT_TIME() < ut.end GROUP BY ut ORDER BY ut.precedence",true);
        if($result==null){
            return false;
        }
        return $result['remaining']<1000;
    }

    public function getClientResponse($mac,$zone){
        $result=$this->fetchResult("SELECT su.name,su.package,ut.id utid,ut.name utname,(su.package-SUM(u.kbytes)) remaining,SUM(u.kbytes) usage,su.comment,su.banner_url,CURRENT_TIMESTAMP () datetime,au.pkey,au.skey FROM AppBundle\Entity\slave_usage u JOIN u.usage_type ut JOIN u.slave_user su JOIN su.auth_user au WHERE au.zone='$zone' AND su.mac='$mac' AND ut.start < CURRENT_TIME() AND CURRENT_TIME() < ut.end GROUP BY ut HAVING remaining>1000 ORDER BY ut.precedence",true);
        if($result == null){
            $result=$this->fetchResult("SELECT su.name,su.package,ut.id utid,ut.name utname,(su.package-SUM(u.kbytes)) remaining,SUM(u.kbytes) usage,su.comment,su.banner_url,CURRENT_TIMESTAMP () datetime,au.pkey,au.skey FROM AppBundle\Entity\slave_usage u JOIN u.usage_type ut JOIN u.slave_user su JOIN su.auth_user au WHERE au.zone='$zone' AND su.mac='$mac' AND ut.start < CURRENT_TIME() AND CURRENT_TIME() < ut.end GROUP BY ut ORDER BY ut.precedence",true);
        }
        return $result;
    }

    public function getPackageDetail(){
        return $this->fetchResult("SELECT su.sid, su.name, su.package FROM AppBundle\Entity\slave_user su JOIN su.auth_user au WHERE au.id=$this->id AND su.mac!='FFFFFFFFFFFF'");
    }

    public function getSharedQuotaByZone($zone){
        // returns available maximum quota for a new user.
        // fetchResult returns array(1 => '200000')
        $result=$this->fetchResult("SELECT SUM(su.package) FROM AppBundle\Entity\slave_user su JOIN su.auth_user au WHERE au.zone='$zone' AND su.mac!='FFFFFFFFFFFF' GROUP BY au",true);
        if(empty($result)){
            $result=[0=>'0'];
        }
        return array_values($result)[0];
    }

    public function getClientBySid($sid,$zone){
        return $this->fetchResult("SELECT su FROM AppBundle\Entity\slave_user su JOIN su.auth_user au WHERE su.sid='$sid' AND au.zone='$zone'",true);
    }

    public function getClientByMac($mac,$zone){
        return $this->fetchResult("SELECT su FROM AppBundle\Entity\slave_user su JOIN su.auth_user au WHERE su.mac='$mac' AND au.zone='$zone'",true);
    }

    public function getUTByUtid($utid,$zone){
        return $this->fetchResult("SELECT ut FROM AppBundle\Entity\usage_type ut JOIN ut.auth_user au WHERE au.zone='$zone' AND ut.id=$utid",true);
    }
}