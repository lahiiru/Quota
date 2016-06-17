<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
class SlavePaymentRepository extends EntityRepository{
    public function getPayments($zone){
        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT p.id as id, su.name as name, su.package as package, p.fee as fee, p.date as date FROM AppBundle\Entity\slave_payment p
                  JOIN p.slave_user su
                  JOIN su.auth_user au
                  WHERE au.zone=:zone"
            )
            ->setParameter('zone', $zone);
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    public function getPaymentsForSlave($mac,$zone){
        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT p.id as id, p.fee as fee, p.date as date FROM AppBundle\Entity\slave_payment p
                  JOIN p.slave_user su
                  JOIN su.auth_user au
                  WHERE su.mac=:mac AND au.zone=:zone"
            )
            ->setParameter('zone', $zone)
            ->setParameter('mac', $mac);

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
}