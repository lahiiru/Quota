<?php

namespace AppBundle\Tests\Entity;
use AppBundle\Entity\SlaveUserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use \Symfony\Bridge\PhpUnit\ClockMock;
/**
 * @group time-sensitive
 */
class SlaveUserRepositoryTest extends WebTestCase
{
    /**
     * @var SlaveUserRepository
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

    public function testNIGHT_OVER_at_NIGHT()
    {

        $date = (new \DateTime("2016-06-05 00:30:00", new \DateTimeZone("Asia/Colombo")))->format('U');
        ClockMock::withClockMock($date);

        $response=$this->SlaveUserRepository->isOver("34238718F80F","NO FREE");
        $this->assertTrue($response);

        ClockMock::withClockMock(false);
    }

    public function testDAY_OVER()
    {

        $date = (new \DateTime("2016-06-05 00:30:00", new \DateTimeZone("Asia/Colombo")))->format('U');
        ClockMock::withClockMock($date);

        $response=$this->SlaveUserRepository->isOver("00000000000002","NO FREE");
        $this->assertTrue($response);

        ClockMock::withClockMock(false);
    }

    public function testBOTH_USED()
    {

        $date = (new \DateTime("2016-06-05 00:30:00", new \DateTimeZone("Asia/Colombo")))->format('U');
        ClockMock::withClockMock($date);

        $response=$this->SlaveUserRepository->isOver("00000000000011","NO FREE");
        $this->assertTrue($response);

        ClockMock::withClockMock(false);
    }
}
