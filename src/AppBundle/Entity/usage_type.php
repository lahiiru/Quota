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
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UsageTypeRepository")
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
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $precedence;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\auth_user", inversedBy="usage_types")
     * @ORM\JoinColumn(name="auth_user", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $auth_user;
    /**
     * @ORM\OneToMany(targetEntity="slave_usage", mappedBy="usage_type")
     */
    protected $slave_usages;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
    public function getPrecedence()
    {
        return $this->precedence;
    }

    /**
     * @return mixed
     */
    public function getAuthUser()
    {
        return $this->auth_user;
    }

    /**
     * @return mixed
     */
    public function getSlaveUsages()
    {
        return $this->slave_usages;
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }
    /**
     * @ORM\Column(type="time", nullable=true)
     */
    protected $start;
    /**
     * @ORM\Column(type="time", nullable=true)
     */
    protected $end;

}