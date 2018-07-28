<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Frequency
 *
 * @ORM\Table(name="frequency")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\FrequencyRepository")
 * @DoctrineAssert\UniqueEntity("id")
 */
class Frequency {

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
     * @ORM\Column(name="time", type="string", length=50)
     * @Assert\NotBlank(message = "field.required")
     */
    private $time;
    
    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="frequency")
     */
    private $tasks;
    
    /**
     * @var string
     *
     * @ORM\Column(name="class_css", type="string", length=50)
     * @Assert\NotBlank(message = "field.required")
     */
    private $class_css;

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
     * Set time
     *
     * @param string $time
     * @return Frequency
     */
    public function setTime($time) {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return string 
     */
    public function getTime() {
        return $this->time;
    }
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add task
     *
     * @param \AppBundle\Entity\Task $task
     * @return Frequency
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
    
    /**
     * Set class_css
     *
     * @param string $class_css
     * @return Frequency
     */
    public function setClassCss($class_css) {
        $this->class_css = $class_css;
        return $this;
    }

    /**
     * Get class_css
     *
     * @return string 
     */
    public function getClassCss() {
        return $this->class_css;
    }
}
