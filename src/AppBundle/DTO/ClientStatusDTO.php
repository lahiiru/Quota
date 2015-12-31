<?php
namespace AppBundle\DTO;
use AppBundle\Entity;
class ClientStatusDTO
{

    private $name;
    private $LLA;
    private $usage;
    private $status;

    /**
     * ClientStatusDTO constructor.
     * @param $name
     * @param $LLA
     * @param $usage
     * @param $status
     */
    public function __construct($name, $LLA, $usage, $status)
    {
        $this->name = $name;
        $this->LLA = $LLA;
        $this->usage = $usage;
        $this->status = $status;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param mixed $LLA
     */
    public function setLLA($LLA)
    {
        $this->LLA = $LLA;
    }

    /**
     * @param mixed $usage
     */
    public function setUsage($usage)
    {
        $this->usage = $usage;
    }

    /**
     * @param Entity\auth_user $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
    public function getLLA()
    {
        return $this->LLA;
    }

    /**
     * @return mixed
     */
    public function getUsage()
    {
        return $this->usage;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

}