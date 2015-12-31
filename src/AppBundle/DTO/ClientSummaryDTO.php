<?php
/**
 * Created by PhpStorm.
 * User: Lahiru
 * Date: 31/12/2015
 * Time: 8:05 PM
 */

namespace AppBundle\DTO;
use AppBundle\Entity;
class ClientSummaryDTO
{
//su.sid,su.name,su.mac,su.state,su.package,SUM(u.kbytes)
    protected $sid;
    protected $name;
    protected $mac;
    protected $state;
    protected $package;
    protected $usage;

    /**
     * ClientSummaryDTO constructor.
     * @param $sid
     * @param $name
     * @param $mac
     * @param $state
     * @param $package
     * @param $total
     */
    public function __construct($sid, $name, $mac, $state, $package, $total)
    {
        $this->sid = $sid;
        $this->name = $name;
        $this->mac = $mac;
        $this->state = $state;
        $this->package = $package;
        $this->usage = $total;
    }

    /**
     * @return mixed
     */
    public function getSid()
    {
        return $this->sid;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getMac()
    {
        return $this->mac;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return mixed
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @return mixed
     */
    public function getUsage()
    {
        return $this->usage;
    }

}