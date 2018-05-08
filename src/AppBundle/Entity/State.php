<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Statu
 *
 * @ORM\Table(name="state")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\StateRepository")
 * @DoctrineAssert\UniqueEntity("name")
 */
class State {

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
     * @var string
     *
     * @ORM\Column(name="name_my_task", type="string", length=50)
     * @Assert\NotBlank(message = "field.required")
     */
    private $name_my_task;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=50)
     */
    private $color;

    /**
     * @var task
     *
     * @ORM\OneToMany(targetEntity="Task", mappedBy="state", cascade={"persist"})
     */
    private $tasks;

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
     * @return Rol
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
     * Set name_my_task
     *
     * @param string $name
     * @return State
     */
    public function setNameMyTask($name) {
        $this->name_my_task = $name;
        return $this;
    }

    /**
     * Get name_my_task
     *
     * @return string
     */
    public function getNameMyTask() {
        return $this->name_my_task;
    }

    /**
     * Set color
     *
     * @param string $color
     * @return State
     */
    public function setColor($color) {
        $this->color = $color;
        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor() {
        return $this->color;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString(){
        return $this->name;
    }

    /**
     * Add task
     *
     * @param \AppBundle\Entity\Task $task
     * @return State
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

}
