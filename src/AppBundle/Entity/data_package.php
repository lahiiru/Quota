<?php
/**
 * Created by PhpStorm.
 * User: Lahiru
 * Date: 26/12/2015
 * Time: 2:51 PM
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_package")
 */
class data_package
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $pid;
    /**
     * @ORM\ManyToOne(targetEntity="auth_user", inversedBy="id")
     * @ORM\JoinColumn(name="auth_user", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $auth_user;
    /**
     * @ORM\Column(type="integer")
     */
    protected $kbytes;
    /**
     * @ORM\Column(type="datetime")
     */
    protected $start;
    /**
     * @ORM\Column(type="datetime")
     */
    protected $end;

    public function __construct($auth_user)
    {
        $this->auth_user=$auth_user;
    }
    /**
     * Set kbytes
     *
     * @param integer $kbytes
     *
     * @return data_package
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
     * Set start
     *
     * @param \DateTime $start
     *
     * @return data_package
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     *
     * @return data_package
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
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
}
