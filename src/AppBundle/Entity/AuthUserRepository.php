<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
class AuthUserRepository extends EntityRepository{

    private function getTimeStamp(){
        return \DateTime::createFromFormat('U', time())->setTimezone(new \DateTimeZone('Asia/Colombo'))->format('Y-m-d H:i:s');
    }

    public function getApparentTime(){
        return $this->getTimeStamp();
    }

    public function getRunningDataPackage($id){

        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT p FROM AppBundle\Entity\data_package p
                  WHERE p.start < :ct AND :ct < p.end AND p.auth_user=:id
                  ORDER BY p.pid DESC"
            )
            ->setParameter('id', $id)
            ->setParameter('ct',$this->getTimeStamp());
        try {
            return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function getRunningDataPackageByZone($zone){
        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT p FROM AppBundle\Entity\data_package p
                  JOIN p.auth_user au
                  WHERE p.start < :ct AND :ct < p.end AND au.zone=:zone
                  ORDER BY p.pid DESC"
            )
            ->setParameter('zone', $zone)
            ->setParameter('ct',$this->getTimeStamp());
        try {
            return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function getSlavePackageDetail($zone){
        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT su.sid, su.name, su.package FROM AppBundle\Entity\slave_user su
                  JOIN su.auth_user au
                  WHERE au.zone=:zone AND su.mac!='FFFFFFFFFFFF'"
            )
            ->setParameter('zone', $zone);
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function getSharedQuotaByZone($zone){
        // returns available maximum quota for a new user.
        // fetchResult returns array(1 => '200000')
        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT SUM(su.package) FROM AppBundle\Entity\slave_user su
                  JOIN su.auth_user au
                  WHERE au.zone=:zone AND su.mac!='FFFFFFFFFFFF'
                  GROUP BY au"
            )
            ->setParameter('zone', $zone);
        try {
            $result = $query->getResult();
            return array_values($result)[0];
        } catch (\Doctrine\ORM\NoResultException $e) {
            return 0;
        }
    }
}