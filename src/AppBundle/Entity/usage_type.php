<?php
/**
 * Created by PhpStorm.
 * User: Lahiru
 * Date: 22/01/2016
 * Time: 11:46 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="usage_type")
 */
class usage_type
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $name;
    /**
     * @ORM\OneToMany(targetEntity="slave_user", mappedBy="slave_user")
     */
    protected $slave_usages;
    /**
     * @ORM\Column(type="time", nullable=true)
     */
    protected $start;
    /**
     * @ORM\Column(type="time", nullable=true)
     */
    protected $end;

}