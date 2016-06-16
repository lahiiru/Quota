<?php

namespace AppBundle\Tests\Entity;
use AppBundle\Entity\AuthUserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\data_package;
use \Symfony\Bridge\PhpUnit\ClockMock;
/**
 * @group time-sensitive
 */
class RequestRepositoryTest extends WebTestCase
{
    /**
     * @var AuthUserRepository
     */
    private $SlaveRequestRepository;

    /**
     * @group time-sensitive
     */
    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->SlaveRequestRepository = $kernel->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:slave_request');
        ClockMock::register(__CLASS__);
    }

    public function testDummy()
    {

        //$tags = $this->SlaveRequestRepository->getRunningDataPackage(22);

        //$this->assertInstanceOf("AppBundle\Entity\data_package",$tags);
    }
}
