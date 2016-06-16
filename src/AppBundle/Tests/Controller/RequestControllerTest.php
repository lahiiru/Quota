<?php

namespace AppBundle\Tests\Controller;
use AppBundle\Entity\AuthUserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\data_package;
use \Symfony\Bridge\PhpUnit\ClockMock;
/**
 * @group time-sensitive
 */
class RequestControllerTest extends WebTestCase
{
    public static function setupBeforeClass()
    {
        ClockMock::register("AppBundle\DQL\FetchData");
    }

    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

   }

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        /*
        $this->AuthUserRepository = $kernel->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:auth_user');
        */
        ClockMock::register("AppBundle\Controller\RequestController");
    }


    /**
     * @group time-sensitive
     */
    public function testNIGHT_OVER_at_NIGHT()
    {
        $date = (new \DateTime("2016-05-05 00:30:00", new \DateTimeZone("Asia/Colombo")))->format('U');
        ClockMock::withClockMock($date);

        $client = static::createClient();

        $crawler = $client->request('GET', '/request/user/NO FREE/34238718F80F/check');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains(__CLASS__.'{"status":"OVER","details":{"name":"0.6GB_NIGHT_OVER","package":600000,"utid":2,"utname":"NIGHT TIME","remaining":"600000","usage":"0"',$crawler->filter('body')->text());

        ClockMock::withClockMock(false);
    }

    public function testNIGHT_OVER_at_DAY()
    {
        $date = (new \DateTime("2016-05-05 08:01:00", new \DateTimeZone("Asia/Colombo")))->format('U');
        ClockMock::withClockMock($date);

        $client = static::createClient();
        $crawler = $client->request('GET', '/request/user/NO FREE/34238718F80F/check');
        //, $crawler->filter('#container h1')->text()
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('{"status":"OK","details":{"name":"0.6GB_NIGHT_OVER","package":600000,"utid":1,"utname":"ANY TIME","remaining":"600000","usage":"0"',$crawler->filter('body')->text());

        ClockMock::withClockMock(false);
    }

    public function testDAY_OVER()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/request/user/NO FREE/00000000000002/check');
        //, $crawler->filter('#container h1')->text()
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('{"status":"OVER","details":{"name":"3GB_DAY_OVER","package":3000000,"utid":1,"utname":"ANY TIME","remaining":"-10000","usage":"3010000"',$crawler->filter('body')->text());
    }

    public function testNEW_USER()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/request/user/NO FREE/00000000000001/check');
        //, $crawler->filter('#container h1')->text()
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('{"status":"NEW","details":12400000}',$crawler->filter('body')->text());
    }
}
