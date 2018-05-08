<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Proyect
 *
 * @ORM\Table(name="proyect")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ProyectRepository")
 * @DoctrineAssert\UniqueEntity("name")
 */
class Proyect {

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
     * @ORM\Column(name="name", type="string", length=50)
     * @Assert\NotBlank(message = "field.required")
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="proyect")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="proyect")
     */
    private $tasks;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=250, nullable=true)
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_time", type="date")
     * @Assert\Date()
     */
    private $created_time;

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
     * Set created_time
     *
     * @param datetime $created_time
     * @return Proyect
     */
    public function setCreatedTime($createdTime) {
        $this->created_time = $createdTime;

        return $this;
    }

    /**
     * Get created_time
     *
     * @return datetime
     */
    public function getCreatedTime() {
        return $this->created_time;
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
     * Constructor
     */
    public function __construct() {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add users
     *
     * @param \AppBundle\Entity\User $users
     * @return Proyect
     */
    public function addUser(\AppBundle\Entity\User $users) {
        $users->addProyect($this);
        $this->users[] = $users;
        return $this;
    }

    /**
     * Remove users
     *
     * @param \AppBundle\Entity\User $users
     */
    public function removeUser(\AppBundle\Entity\User $users) {
        $this->users->removeElement($users);
        $users->getProyect()->removeElement($this);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers() {
        return $this->users;
    }

    /**
     * Add task
     *
     * @param \AppBundle\Entity\Task $task
     * @return Proyect
     */
    public function addTask(\AppBundle\Entity\Task $task) {
        $this->tasks[] = $task;
        return $this;
    }

    /**
     * Remove task
     *
     * @param \AppBundle\Entity\Task $task
     */
    public function removeTask(\AppBundle\Entity\Task $task) {
        $this->tasks->removeElement($task);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTasks() {
        return $this->tasks;
    }

    public function __toString(){
        return $this->name;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Proyect
     */
    public function setEnabled($enabled) {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled() {
        return $this->enabled;
    }

}
