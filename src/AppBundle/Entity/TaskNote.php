<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * taskNote
 *
 * @ORM\Table(name="tasknote")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TaskNoteRepository")
 */
class TaskNote {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
 * @var \Task
 *
 * @ORM\ManyToOne(targetEntity="Task", inversedBy="notes")
 * @ORM\JoinColumns({
 *   @ORM\JoinColumn(name="task", referencedColumnName="id", onDelete="Cascade")
 * })
 */
    protected $task;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="notes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user", referencedColumnName="id", onDelete="Cascade")
     * })
     */
    protected $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdOn", type="date")
     * @Assert\Date()
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private $note;

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
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return TaskNote
     */
    public function setUser(\AppBundle\Entity\User $user){
        $this->user = $user;
        return $this;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return TaskNote
     */
    public function getUser(){
        return $this->user;
    }

    /**
     * Set task
     *
     * @param \AppBundle\Entity\Task $task
     * @return TaskNote
     */
    public function setTask(\AppBundle\Entity\Task $task){
        $this->task = $task;
        return $this;
    }
    /**
     * Get task
     *
     * @return \AppBundle\Entity\Task
     */
    public function getTask(){
        return $this->task;
    }

    /**
     * Set createdOn
     *
     * @param datetime $createdOn
     * @return TaskNote
     */
    public function setCreatedOn($createdOn) {
        $this->createdOn = $createdOn;
        return $this;
    }

    /**
     * Get createdOn
     *
     * @return datetime
     */
    public function getCreatedOn() {
        return $this->createdOn;
    }

    /**
     * Set note
     *
     * @param string $dnote
     * @return TaskNote
     */
    public function setNote($note) {
        $this->note = $note;
        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote() {
        return $this->note;
    }
}
