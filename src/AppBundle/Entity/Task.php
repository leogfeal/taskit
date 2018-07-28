<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Task
 *
 * @ORM\Table(name="task")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TaskRepository")
 * @DoctrineAssert\UniqueEntity("name")
 */
class Task {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     * @Assert\NotBlank(message = "field.required")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
    
    /**
     * @var string
     *
     * @ORM\Column(name="instructions", type="text", nullable=true)
     */
    private $instructions;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="date", nullable=true)
     * @Assert\Date()
     */
    private $start_time;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_time", type="date")
     * @Assert\Date()
     */
    private $end_time;

    /**
     * @var \State
     *
     * @ORM\ManyToOne(targetEntity="State", inversedBy="tasks", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="state", referencedColumnName="id", onDelete="Cascade", nullable=true)
     * })
     */
    private $state;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tasks_created", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_created_task", referencedColumnName="id", onDelete="Cascade", nullable=true)
     * })
     */
    private $user_created_task; // usuario que creo la tarea

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tasks_assigned", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user", referencedColumnName="id", onDelete="Cascade", nullable=true)
     * })
     */
    private $user; //usuario al que se le asigno la tarea

    /**
     * @var \Proyect
     *
     * @ORM\ManyToOne(targetEntity="Proyect", inversedBy="tasks", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="proyect", referencedColumnName="id", onDelete="Cascade", nullable=true)
     * })
     */
    private $proyect;
    
     /**
     * @var \Frequency
     *
     * @ORM\ManyToOne(targetEntity="Frequency", inversedBy="tasks", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="frequency", referencedColumnName="id", onDelete="Cascade", nullable=true)
     * })
     */
    private $frequency;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="frecuency_date", type="date", nullable=true)
     * @Assert\Date()
     */
    private $frequency_date;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="frequency_enable", type="boolean")
     */
    private $frequency_enable;

    /**
     * @ORM\OneToMany(targetEntity="TaskNote", mappedBy="task", cascade={"persist"})
     */
    private $notes;

    /**
     * @ORM\OneToMany(targetEntity="Attached", mappedBy="task", cascade={"persist"})
     */
    private $attached;

    /**
     * @var string
     *
     * @ORM\Column(name="priority", type="string", length=50, nullable=false)
     */
    private $priority;

    /**
     * Set priority
     *
     * @param string $priority
     *
     * @return Task
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Get priority
     *
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Proyect
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set start_time
     *
     * @param datetime $start_time
     * @return Task
     */
    public function setStartTime($startTime) {
        $this->start_time = $startTime;
        return $this;
    }

    /**
     * Get start_time
     *
     * @return datetime
     */
    public function getStartTime() {
        return $this->start_time;
    }

    /**
     * Set end_time
     *
     * @param datetime $end_time
     * @return Task
     */
    public function setEndTime($endTime) {
        $this->end_time = $endTime;
        return $this;
    }

    /**
     * Get end_time
     *
     * @return datetime
     */
    public function getEndTime() {
        return $this->end_time;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Proyect
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }
    
    /**
     * Set instructions
     *
     * @param string $instructions
     * @return Task
     */
    public function setInstructions($instructions) {
        $this->instructions = $instructions;
        return $this;
    }

    /**
     * Get instructions
     *
     * @return string
     */
    public function getInstructions() {
        return $this->instructions;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->notes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString(){
        return $this->name;
    }

    /**
     * Set state
     *
     * @param \AppBundle\Entity\State $state
     * @return Task
     */
    public function setState(\AppBundle\Entity\State $state) {
        $this->state = $state;
        return $this;
    }

    /**
     * Get state
     *
     * @return \AppBundle\Entity\State
     */
    public function getState() {
        return $this->state;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Task
     */
    public function setUser($user) {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set user_created_task
     *
     * @param \AppBundle\Entity\User $user_created_task
     * @return Task
     */
    public function setUserCreatedTask(\AppBundle\Entity\User $user_created_task) {
        $this->user_created_task = $user_created_task;
        return $this;
    }

    /**
     * Get user_created_task
     *
     * @return \AppBundle\Entity\User
     */
    public function getUserCreatedTask() {
        return $this->user_created_task;
    }
    
    public function setAttached(\Doctrine\Common\Collections\Collection $attached){
        $this->attached = $attached;
    }

    /**
     * Add attached
     *
     * @param \AppBundle\Entity\Attached $attached
     * @return Task
     */
    public function addAttached(\AppBundle\Entity\Attached $attached)
    {
        $attached->setTask($this);
        $this->attached[] = $attached;
        return $this;
    }

    public function removeAllAttached($path){
        $list_attached = $this->getAttached();
        foreach($list_attached as $file){
            @unlink($path.'img/attached/'.$file->getName());
            $this->removeAttached($file);
        }
    }

    /**
     * Remove attached
     *
     * @param \AppBundle\Entity\Attached $attached
     */
    public function removeAttached(\AppBundle\Entity\Attached $attached)
    {
        $this->attached->removeElement($attached);
    }

    /**
     * Get attached
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttached()
    {
        return $this->attached;
    }

    /**
     * Set proyect
     *
     * @param \AppBundle\Entity\Proyect $proyect
     * @return Task
     */
    public function setProyect(\AppBundle\Entity\Proyect $proyect) {
        $this->proyect = $proyect;
        return $this;
    }

    /**
     * Get proyect
     *
     * @return \AppBundle\Entity\Proyect
     */
    public function getProyect() {
        if($this->proyect)
            return $this->proyect->getId();
        else
            return $this->proyect;
    }

    public function getObjProyect() {
        return $this->proyect;
    }
    
    /**
     * Set frequency
     *
     * @param \AppBundle\Entity\Frequency $frequency OR NULL
     * @return Task
     */
    public function setFrequency($frequency) {
        $this->frequency = $frequency;
        return $this;
    }

    /**
     * Get frequency
     *
     * @return \AppBundle\Entity\Frequency
     */
    public function getFrequency() {
        return $this->frequency;
    }
    
    /**
     * Set frequency_date
     *
     * @param datetime $frequency_date
     * @return Task
     */
    public function setFrequencyDate($frequency_date) {
        $this->frequency_date = $frequency_date;
        return $this;
    }

    /**
     * Get frequency_date
     *
     * @return datetime
     */
    public function getFrequencyDate() {
        return $this->frequency_date;
    }
    
    /**
     * Set frequency_enable
     *
     * @param boolean $frequency_enable
     * @return Task
     */
    public function setFrequencyEnable($frequency_enable) {
        $this->frequency_enable = $frequency_enable;
        return $this;
    }

    /**
     * Get frequency_enable
     *
     * @return boolean
     */
    public function getFrequencyEnable() {
        return $this->frequency_enable;
    }

    /**
     * Add note
     *
     * @param \AppBundle\Entity\TaskNote $note
     * @return Task
     */
    public function addNote(\AppBundle\Entity\TaskNote $note)
    {
        $this->notes[] = $note;
        return $this;
    }

    /**
     * Remove notes
     *
     * @param \AppBundle\Entity\TaskNotes $notes
     */
    public function removeNotes(\AppBundle\Entity\TaskNote $note)
    {
        $this->notes->removeElement($note);
    }

    /**
     * Get notes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotes()
    {
        return $this->notes;
    }
}
