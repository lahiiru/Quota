<?php
/**
 * Created by PhpStorm.
 * User: Lahiru
 * Date: 26/12/2015
 * Time: 2:51 PM
 */

namespace AppBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="auth_user")
 * @ORM\AttributeOverrides({@ORM\AttributeOverride(name="username", column=@ORM\Column(type="string", name="username", length=255)),})
 * @ORM\AttributeOverrides({@ORM\AttributeOverride(name="usernameCanonical", column=@ORM\Column(type="string", name="username_canonical", length=255)),})
 */
class auth_user extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\OneToMany(targetEntity="data_package", mappedBy="auth_user")
     */
    protected $packages;
    /**
     * @ORM\OneToMany(targetEntity="slave_user", mappedBy="auth_user")
     */
    protected $slave_users;
    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $link;
    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    protected $auth_type;
    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $picture;
    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $zone;
    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $pkey;
    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $skey;
    /** @ORM\Column(name="uid", type="string", length=255) */
    protected $uid;

    /** @ORM\Column(name="access_token", type="string", length=255, nullable=true) */
    protected $access_token;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $registedOn;

    /**
     * @return mixed
     */
    public function getSkey()
    {
        return $this->skey;
    }

    /**
     * @param mixed $skey
     */
    public function setSkey($skey)
    {
        $this->skey = $skey;
    }

    /**
     * @return mixed
     */
    public function getPkey()
    {
        return $this->pkey;
    }

    /**
     * @param mixed $pkey
     */
    public function setPkey($pkey)
    {
        $this->pkey = $pkey;
    }

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * @return mixed
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * @param mixed $packages
     */
    public function setPackages($packages)
    {
        $this->packages = $packages;
    }

    /**
     * @return mixed
     */
    public function getSlaveUsers()
    {
        return $this->slave_users;
    }

    /**
     * @param mixed $slave_users
     */
    public function setSlaveUsers($slave_users)
    {
        $this->slave_users = $slave_users;
    }
    

    /**
     * Get aid
     *
     * @return integer
     */
    public function getAid()
    {
        return $this->aid;
    }

    /**
     * Set link
     *
     * @param string $link
     *
     * @return auth_user
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set picture
     *
     * @param string $picture
     *
     * @return auth_user
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set zone
     *
     * @param string $zone
     *
     * @return auth_user
     */
    public function setZone($zone)
    {
        $this->zone = $zone;

        return $this;
    }

    /**
     * Get zone
     *
     * @return string
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * Set uid
     *
     * @param string $uid
     *
     * @return auth_user
     */
    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
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
     * Set accessToken
     *
     * @param string $accessToken
     *
     * @return auth_user
     */
    public function setAccessToken($accessToken)
    {
        $this->access_token = $accessToken;

        return $this;
    }

    /**
     * Get accessToken
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * Set registedOn
     *
     * @param \DateTime $registedOn
     *
     * @return auth_user
     */
    public function setRegistedOn($registedOn)
    {
        $this->registedOn = $registedOn;

        return $this;
    }

    /**
     * Get registedOn
     *
     * @return \DateTime
     */
    public function getRegistedOn()
    {
        return $this->registedOn;
    }

    /**
     * Set authType
     *
     * @param string $authType
     *
     * @return auth_user
     */
    public function setAuthType($authType)
    {
        $this->auth_type = $authType;

        return $this;
    }

    /**
     * Get authType
     *
     * @return string
     */
    public function getAuthType()
    {
        return $this->auth_type;
    }
}
