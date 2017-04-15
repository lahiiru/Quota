<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
class UsageTypeRepository extends EntityRepository{

    private function getTimeStamp(){
        return \DateTime::createFromFormat('U', time())->setTimezone(new \DateTimeZone('Asia/Colombo'))->format('Y-m-d H:i:s');
    }

    public function getRunningUsageType($zone,$mac){
        // Finding for non over highest precedent usage type for a given client.
        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT ut FROM AppBundle\Entity\slave_usage u
                  JOIN u.usage_type ut
                  JOIN u.slave_user su
                  JOIN su.auth_user au
                  WHERE au.zone=:zone AND su.mac=:mac AND ut.start < :ct AND :ct < ut.end
                  GROUP BY ut HAVING (su.package-SUM(u.kbytes))>1000
                  ORDER BY ut.precedence"
            )
            ->setParameter('zone', $zone)
            ->setParameter('mac', $mac)
            ->setParameter('ct', $this->getTimeStamp());
        try {
            return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            // All packages types are over
            return null;
        }
    }

    public function getUsageTypes($au)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT ut FROM AppBundle\Entity\usage_type ut WHERE ut.auth_user=:au"
             )
            ->setParameter('au', $au);
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

}