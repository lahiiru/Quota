<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
class SlaveRequestRepository extends EntityRepository{

    private function getTimeStamp(){
        return \DateTime::createFromFormat('U', time())->setTimezone(new \DateTimeZone('Asia/Colombo'))->format('Y-m-d H:i:s');
    }

    public function getRequests($id, $zone=''){
        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT sr as request,su as slave_user FROM AppBundle\Entity\slave_request sr
                  JOIN sr.slave_user su
                  JOIN su.auth_user au
                  WHERE (au.zone=:zone OR au.id=:id) AND sr.pending=1"
            )
            ->setParameter('zone', $zone)
            ->setParameter('id', $id);
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
}