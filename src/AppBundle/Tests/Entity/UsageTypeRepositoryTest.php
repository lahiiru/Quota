<?php

namespace AppBundle\Tests\Entity;
use AppBundle\Entity\AuthUserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\data_package;
use \Symfony\Bridge\PhpUnit\ClockMock;
/**
 * @group time-sensitive
 */
class UsageTypeRepositoryTest extends WebTestCase
{
    /**
     * @var AuthUserRepository
     */
    private $AuthUserRepository;

    /**
     * @group time-sensitive
     */
    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->AuthUserRepository = $kernel->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:auth_user');
        ClockMock::register(__CLASS__);
    }

    public function testClockMock()
    {
        $date = (new \DateTime("2016-11-05 01:00:00", new \DateTimeZone("Asia/Colombo")))->format('U');
        ClockMock::withClockMock($date);
        $time = $this->AuthUserRepository->getApparentTime();
        $this->assertEquals("2016-11-05 01:00:00",$time);
        ClockMock::withClockMock(false);
    }

}
