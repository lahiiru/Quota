<?php
/**
 * Created by PhpStorm.
 * User: Lahiru
 * Date: 26/12/2015
 * Time: 5:01 PM
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="slave_user")
 */

class slave_user
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $sid;
    /**
     * @ORM\Column(type="string", length=16)
     */
    protected $mac;
    /**
     * @ORM\ManyToOne(targetEntity="auth_user", inversedBy="slave_users")
     * @ORM\JoinColumn(name="auth_user", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $auth_user;
    /**
     * @ORM\OneToMany(targetEntity="slave_usage", mappedBy="slave_user")
     */
    protected $slave_usages;
    /**
     * @ORM\OneToMany(targetEntity="slave_payment", mappedBy="slave_user")
     */
    protected $slave_payments;
    /**
     * @ORM\OneToMany(targetEntity="slave_request", mappedBy="slave_user")
     */
    protected $slave_requests;
    /**
     * @ORM\Column(type="string", length=30)
     */
    protected $name;
    /**
     * @ORM\Column(type="integer", length=1)
     */
    protected $state;
    /**
     * @ORM\Column(type="integer")
     */
    protected $package;
    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $comment;

    /**
     * slave_user constructor.
     * @param $mac
     * @param $auth_user
     * @param $name
     */
    public function __construct($mac, $auth_user, $name,$state,$package)
    {
        $this->mac = $mac;
        $this->auth_user = $auth_user;
        $this->name = $name;
        $this->state = $state;
        $this->package = $package;
        $this->comment="";
    }

    /**
     * @return mixed
     */
    public function getSlaveRequests()
    {
        return $this->slave_requests;
    }

    /**
     * @param mixed $slave_requests
     */
    public function setSlaveRequests($slave_requests)
    {
        $this->slave_requests = $slave_requests;
    }

    /**
     * @return mixed
     */
    public function getSlaveUsages()
    {
        return $this->slave_usages;
    }

    /**
     * @param mixed $slave_usages
     */
    public function setSlaveUsages($slave_usages)
    {
        $this->slave_usages = $slave_usages;
    }

    /**
     * @return mixed
     */
    public function getSlavePayments()
    {
        return $this->slave_payments;
    }

    /**
     * @param mixed $slave_payments
     */
    public function setSlavePayments($slave_payments)
    {
        $this->slave_payments = $slave_payments;
    }


    /**
     * Get mac
     *
     * @return string
     */
    public function getMac()
    {
        return $this->mac;
    }


    /**
     * Get uid
     *
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return slave_user
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return slave_user
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return slave_user
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Get sid
     *
     * @return integer
     */
    public function getSid()
    {
        return $this->sid;
    }


    /**
     * Get authUser
     *
     * @return \AppBundle\Entity\auth_user
     */
    public function getAuthUser()
    {
        return $this->auth_user;
    }

    /**
     * @return mixed
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @param mixed $package
     */
    public function setPackage($package)
    {
        $this->package = $package;
    }

}
