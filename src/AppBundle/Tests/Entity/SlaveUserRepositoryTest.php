<?php

namespace AppBundle\Tests\Entity;
use AppBundle\Entity\AuthUserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\data_package;
use \Symfony\Bridge\PhpUnit\ClockMock;
/**
 * @group time-sensitive
 */
class SlaveUserRepositoryTest extends WebTestCase
{
    /**
     * @var AuthUserRepository
     */
    private $SlaveUserRepository;

    /**
     * @group time-sensitive
     */
    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->SlaveUserRepository = $kernel->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:slave_user');
        ClockMock::register(__CLASS__);
    }

    public function testClockMock()
    {
        $date = (new \DateTime("2016-11-05 01:00:00", new \DateTimeZone("Asia/Colombo")))->format('U');
        ClockMock::withClockMock($date);
        $time = $this->SlaveUserRepository->getApparentTime();
        $this->assertEquals("2016-11-05 01:00:00",$time);
        ClockMock::withClockMock(false);
    }

}
