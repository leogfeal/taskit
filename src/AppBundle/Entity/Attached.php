<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Attached
 *
 * @ORM\Table(name="attached")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\AttachedRepository")
 */
class Attached {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="attached", type="string", length=150, nullable=false)
     * @Assert\File(maxSize = "10M")
     */
    protected $attached;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var \Task
     *
     * @ORM\ManyToOne(targetEntity="Task", inversedBy="attached")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="task", referencedColumnName="id", onDelete="Cascade")
     * })
     */
    protected $task;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Constructor
     */
    public function __construct() {

    }

    /**
     * Set task
     *
     * @param \AppBundle\Entity\Task $task
     * @return Attached
     */
    public function setTask(\AppBundle\Entity\Task $task)
    {
        $this->task = $task;
        return $this;
    }

    /**
     * Get task
     *
     * @return \AppBundle\Entity\Task
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Set attached
     *
     * @param string $attached
     * @return Attached
     */
    public function setAttached($attached)
    {
        $this->attached = $attached;
        return $this;
    }

    /**
     * Get attached
     *
     * @return string
     */
    public function getAttached()
    {
        return $this->attached;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Attached
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

    public function uploadFile($path)
    {
        if($this->attached === null)
            return false;
        $attachedName = uniqid('attached-').'.'.$this->attached->getClientOriginalExtension();
        $this->attached->move($path, $attachedName);
        $this->setAttached($attachedName);
        return true;
    }
}
