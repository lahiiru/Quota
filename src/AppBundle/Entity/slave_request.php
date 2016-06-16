<?php
/**
 * Created by PhpStorm.
 * User: Lahiru
 * Date: 02/01/2016
 * Time: 1:10 PM
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="slave_request")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\SlaveRequestRepository")
 */
class slave_request
{
    /**
     * @ORM\ManyToOne(targetEntity="slave_user", inversedBy="slave_requests")
     * @ORM\JoinColumn(name="slave_user", referencedColumnName="sid", onDelete="CASCADE")
     */
    protected $slave_user;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $request_id;
    /**
     * @ORM\Column(type="datetime")
     */
    protected $date;
    /**
     * @ORM\Column(type="json_array")
     */
    protected $request_data;
    /**
     * @ORM\Column(type="integer")
     */
    protected $pending;

    /**
     * @return mixed
     */
    public function getPending()
    {
        return $this->pending;
    }

    /**
     * @param mixed $pending
     */
    public function setPending($pending)
    {
        $this->pending = $pending;
    }


    /**
     * Get requestId
     *
     * @return integer
     */
    public function getRequestId()
    {
        return $this->request_id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return slave_request
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
     * Set requestData
     *
     * @param string $requestData
     *
     * @return slave_request
     */
    public function setRequestData($requestData)
    {
        $this->request_data = $requestData;

        return $this;
    }

    /**
     * Get requestData
     *
     * @return string
     */
    public function getRequestData()
    {
        return $this->request_data;
    }

    /**
     * Set slaveUser
     *
     * @param \AppBundle\Entity\slave_user $slaveUser
     *
     * @return slave_request
     */
    public function setSlaveUser(\AppBundle\Entity\slave_user $slaveUser = null)
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
}
