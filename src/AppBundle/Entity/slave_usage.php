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
    protected $kbytes;

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
     *  @PrePersist
     */
    public function doStuffOnPrePersist()
    {
        $this->date = date('Y-m-d H:i:s');
    }


}
