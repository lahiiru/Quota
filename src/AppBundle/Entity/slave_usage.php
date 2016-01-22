<?php
/**
 * Created by PhpStorm.
 * User: Lahiru
 * Date: 26/12/2015
 * Time: 5:11 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="slave_usage")
 */
class slave_usage
{
    /**
     * @ORM\ManyToOne(targetEntity="slave_user", inversedBy="slave_usages")
     * @ORM\JoinColumn(name="slave_user", referencedColumnName="sid", onDelete="CASCADE")
     */
    protected $slave_user;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $usageid;
    /**
     * @ORM\Column(type="datetime")
     */
    protected $date;
    /**
     * @ORM\Column(type="integer")
     */
    protected $kbytes;
    /**
     * @ORM\ManyToOne(targetEntity="usage_type", inversedBy="slave_usage")
     * @ORM\JoinColumn(name="usage_type", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $usage_type;
    /**
     * slave_usage constructor.
     * @param $slave_user
     * @param $date
     * @param $kbytes
     */
    public function __construct($slave_user, $date, $kbytes)
    {
        $this->slave_user = $slave_user;
        $this->date = $date;
        $this->kbytes = $kbytes;
    }



    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return slave_usage
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set kbytes
     *
     * @param integer $kbytes
     *
     * @return slave_usage
     */
    public function setKbytes($kbytes)
    {
        $this->kbytes = $kbytes;

        return $this;
    }

    /**
     * Get kbytes
     *
     * @return integer
     */
    public function getKbytes()
    {
        return $this->kbytes;
    }

    /**
     * Set slaveUser
     *
     * @param \AppBundle\Entity\slave_user $slaveUser
     *
     * @return slave_usage
     */
    public function setSlaveUser(\AppBundle\Entity\slave_user $slaveUser)
    {
        $this->slave_user = $slaveUser;

        return $this;
    }

    /**
     * Get slaveUser
     *
     * @return \AppBundle\Entity\slave_user
     */
    public function getSlaveUser()
    {
        return $this->slave_user;
    }

    /**
     * Get usageid
     *
     * @return integer
     */
    public function getUsageid()
    {
        return $this->usageid;
    }
}
