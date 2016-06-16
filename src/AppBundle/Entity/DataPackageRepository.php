<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
class DataPackageRepository extends EntityRepository{

    private function getTimeStamp(){
        return \DateTime::createFromFormat('U', time())->setTimezone(new \DateTimeZone('Asia/Colombo'))->format('Y-m-d H:i:s');
    }

    public function getRunningDataPackageByZone($zone){
        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT p FROM AppBundle\Entity\data_package p
                  JOIN p.auth_user au
                  WHERE p.start < :ct AND CURRENT_TIMESTAMP() < p.end AND au.zone=:zone
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
}