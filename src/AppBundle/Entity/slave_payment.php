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
 * @ORM\Table(name="slave_payment")
 */
class slave_payment
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="slave_user", inversedBy="sid")
     * @ORM\JoinColumn(name="slave_user", referencedColumnName="sid", onDelete="CASCADE")
     */
    protected $slave_user;
    /**
     * @ORM\Id
     * @ORM\Column(type="datetime")
     */
    protected $date;
    /**
     * @ORM\Column(type="integer")
     */
    protected $fee;

    /**
     * slave_payment constructor.
     * @param $slave_user
     * @param $date
     * @param $fee
     */
    public function __construct($slave_user, $date, $fee)
    {
        $this->slave_user = $slave_user;
        $this->date = $date;
        $this->fee = $fee;
    }


    /**
     *  @PrePersist
     */
    public function doStuffOnPrePersist()
    {
        $this->date = date('Y-m-d H:i:s');
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return slave_payment
     */
    private function setDate($date)
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
     * Get fee
     *
     * @return integer
     */
    public function getFee()
    {
        return $this->fee;
    }

}
