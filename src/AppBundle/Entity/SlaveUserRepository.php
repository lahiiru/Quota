<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
class SlaveUserRepository extends EntityRepository{
    private function getTimeStamp(){
        return \DateTime::createFromFormat('U', time())->setTimezone(new \DateTimeZone('Asia/Colombo'))->format('Y-m-d H:i:s');
    }

    public function getUsagesByGroupByUt($mac,$zone){
        $cp=$this->getEntityManager()
            ->getRepository("AppBundle:auth_user")
            ->getRunningDataPackageByZone($zone);

        $st = $cp->getStart()->format('Y-m-d H:i:s');
        $end = $cp->getEnd()->format('Y-m-d H:i:s');

        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT su.name,su.package,ut.id utid,ut.name utname, ut.precedence, (su.package-SUM(u.kbytes)) remaining,SUM(u.kbytes) usage FROM AppBundle\Entity\slave_usage u
                  JOIN u.usage_type ut
                  JOIN u.slave_user su
                  JOIN su.auth_user au
                  WHERE au.zone=:zone AND su.mac=:mac AND ut.start < :ct AND :ct < ut.end AND u.date > :st AND u.date < :end
                  GROUP BY ut
                  ORDER BY ut.precedence"
            )
            ->setParameter('mac', $mac)
            ->setParameter('zone', $zone)
            ->setParameter('st',$st)
            ->setParameter('end',$end)
            ->setParameter('ct',$this->getTimeStamp());

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }

    }

    public function isOver($mac,$zone){
        $cp=$this->getEntityManager()
            ->getRepository("AppBundle:auth_user")
            ->getRunningDataPackageByZone($zone);

        $st = $cp->getStart()->format('Y-m-d H:i:s');
        $end = $cp->getEnd()->format('Y-m-d H:i:s');

        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT (su.package-SUM(u.kbytes)) remaining FROM AppBundle\Entity\slave_usage u
                  JOIN u.usage_type ut
                  JOIN u.slave_user su
                  JOIN su.auth_user au
                  WHERE au.zone=:zone AND su.mac=:mac AND ut.start < :ct AND :ct < ut.end AND u.date > :st AND u.date < :end
                  GROUP BY ut
                  HAVING remaining>1000
                  ORDER BY ut.precedence"
            )
            ->setParameter('mac', $mac)
            ->setParameter('zone', $zone)
            ->setParameter('st',$st)
            ->setParameter('end',$end)
            ->setParameter('ct',$this->getTimeStamp());

        try {
            $result = $query->getSingleResult();
            return false;
        } catch (\Doctrine\ORM\NoResultException $e) {
            return true;
        }

    }

    public function  hasNoUsage($mac,$zone){
        $cp=$this->getEntityManager()
            ->getRepository("AppBundle:auth_user")
            ->getRunningDataPackageByZone($zone);

        $st = $cp->getStart()->format('Y-m-d H:i:s');
        $end = $cp->getEnd()->format('Y-m-d H:i:s');

        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT 1 FROM AppBundle\Entity\slave_usage u
                  JOIN u.slave_user su
                  JOIN su.auth_user au
                  WHERE au.zone=:zone AND su.mac=:mac AND u.date > :st AND u.date < :end"
            )
            ->setParameter('mac', $mac)
            ->setParameter('zone', $zone)
            ->setParameter('st',$st)
            ->setParameter('end',$end);
        try {
            $result=$query->getResult();
            return false;
        } catch (\Doctrine\ORM\NoResultException $e) {
            return true;
        }
    }

    public function getClientResponse($mac,$zone){
        $cp=$this->getEntityManager()
            ->getRepository("AppBundle:auth_user")
            ->getRunningDataPackageByZone($zone);

        $st = $cp->getStart()->format('Y-m-d H:i:s');
        $end = $cp->getEnd()->format('Y-m-d H:i:s');

        $dql="  SELECT su.name,su.package,ut.id utid,ut.name utname,(su.package-SUM(u.kbytes)) remaining,SUM(u.kbytes) usage,'$end' expired,su.comment,su.banner_url,CURRENT_TIMESTAMP () datetime,au.pkey,au.skey
                  FROM AppBundle\Entity\slave_usage u
                JOIN u.usage_type ut
                JOIN u.slave_user su
                JOIN su.auth_user au
                WHERE au.zone=:zone
                  AND su.mac=:mac
                  AND ut.start < :ct
                  AND :ct < ut.end
                  AND u.date > :st
                  AND u.date < :end
                GROUP BY ut
                HAVING remaining>1000
                ORDER BY ut.precedence";
        if($this->hasNoUsage($mac,$zone)){
            $dql="  SELECT su.name,su.package,ut.id utid,ut.name utname,(su.package-SUM(u.kbytes)) remaining,SUM(u.kbytes) usage,'$end' expired,su.comment,su.banner_url,CURRENT_TIMESTAMP () datetime,au.pkey,au.skey
                      FROM AppBundle\Entity\slave_usage u
                    JOIN u.usage_type ut
                    JOIN u.slave_user su
                    JOIN su.auth_user au
                    WHERE au.zone=:zone
                      AND su.mac=:mac
                      AND ut.start < :ct
                      AND :ct < ut.end
                    GROUP BY ut
                    ORDER BY ut.precedence";
        }elseif($this->isOver($mac,$zone)){
            $dql="  SELECT su.name,su.package,ut.id utid,ut.name utname,(su.package-SUM(u.kbytes)) remaining,SUM(u.kbytes) usage,'$end' expired,su.comment,su.banner_url,CURRENT_TIMESTAMP () datetime,au.pkey,au.skey
                      FROM AppBundle\Entity\slave_usage u
                    JOIN u.usage_type ut
                    JOIN u.slave_user su
                    JOIN su.auth_user au
                    WHERE au.zone=:zone
                      AND su.mac=:mac
                      AND ut.start < :ct
                      AND :ct < ut.end
                      AND u.date > :st
                      AND u.date < :end
                    GROUP BY ut
                    ORDER BY ut.precedence";
        }

        $query = $this->getEntityManager()
            ->createQuery(
                $dql
            )
            ->setParameter('mac', $mac)
            ->setParameter('zone', $zone)
            ->setParameter('st',$st)
            ->setParameter('end',$end)
            ->setParameter('ct',$this->getTimeStamp());

        try {
            $result = $query->getSingleResult();
            return $result;
        } catch (\Doctrine\ORM\NoResultException $e) {
            return "no";
        }

    }

    public function getClientStatus($mac,$zone){
        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT su.state FROM AppBundle\Entity\slave_user su
                  JOIN su.auth_user au
                  WHERE su.mac=:mac AND au.zone=:zone"
            )
            ->setParameter('mac', $mac)
            ->setParameter('zone', $zone);
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function getApparentTime(){
        return $this->getTimeStamp();
    }

}