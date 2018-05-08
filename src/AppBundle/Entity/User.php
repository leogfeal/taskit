<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Rol;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserRepository")
 * @DoctrineAssert\UniqueEntity("email")
 * @DoctrineAssert\UniqueEntity("username")
 */
class User implements AdvancedUserInterface
{
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
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=150, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255, nullable=false)
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="rol", type="string", length=100, nullable=true)
     */
    private $rol;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @var boolean
     *
     * @ORM\Column(name="locked", type="boolean")
     */
    private $locked;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100)
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20)
     */
    private $phone;

    /**
     * @var proyect
     *
     * @ORM\ManyToMany(targetEntity="Proyect", inversedBy="users", cascade={"persist"})
     */
    private $proyect;

    /**
     * @var task
     *
     * @ORM\OneToMany(targetEntity="Task", mappedBy="user", cascade={"persist"})
     */
    private $tasks_assigned; //tareas asignadas a mi

    /**
     * @var task_created
     *
     * @ORM\OneToMany(targetEntity="Task", mappedBy="user_created_task", cascade={"persist"})
     */
    private $tasks_created; //tareas creadas por mi

    /**
     * @var notes
     * @ORM\OneToMany(targetEntity="TaskNote", mappedBy="user", cascade={"persist"})
     */
    private $notes;

    /**
     * @var string
     *
     * @ORM\Column(name="recovery_code", type="string", length=6, nullable=true)
     */
    private $recovery_code;

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
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

    /**
     * Set name
     *
     * @param string $address
     *
     * @return User
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
    
        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }


    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    
        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
 * Set rol
 *
     * @param string
 *
 * @return User
 */
    public function setRol($rol)
    {
        $this->rol = $rol;

        return $this;
    }

    /**
     * Get rol
     *
     * @return string
     */
    public function getRol()
    {
        return $this->rol;
    }


    public function eraseCredentials() {

    }

    public function getRoles() {
        return array($this->rol);
    }

    public function isAccountNonExpired() {
        return true;
    }

    public function isAccountNonLocked() {
        return $this->locked ? false : true;
    }

    public function isCredentialsNonExpired() {
        return true;
    }

    public function isEnabled() {
        return $this->enabled;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return User
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

    /**
     * Set locked
     *
     * @param boolean $locked
     * @return User
     */
    public function setLocked($locked) {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean
     */
    public function getLocked() {
        return $this->locked;
    }

    public function __toString() {
        return $this->name;
    }

    /**
     * Add proyect
     *
     * @param \AppBundle\Entity\Proyect $proyect
     * @return User
     */
    public function addProyect(\AppBundle\Entity\Proyect $proyect)
    {
        $this->proyect[] = $proyect;
        return $this;
    }

    /**
     * Remove proyect
     *
     * @param \AppBundle\Entity\Proyect $proyect
     */
    public function removeProyect(\AppBundle\Entity\Proyect $proyect)
    {
        $this->proyect->removeElement($proyect);
    }

    /**
     * Get proyect
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProyect()
    {
        return $this->proyect;
    }

    /**
     * Add task_assigned
     *
     * @param \AppBundle\Entity\Task $task
     * @return User
     */
    public function addTaskAssigned(\AppBundle\Entity\Task $task) {
        $this->tasks_assigned[] = $task;
        return $this;
    }

    /**
     * Remove task_assigned
     *
     * @param \AppBundle\Entity\Task $task
     */
    public function removeTaskAssigned(\AppBundle\Entity\Task $task) {
        $this->tasks_assigned->removeElement($task);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTasksAssigned() {
        return $this->tasks_assigned;
    }

    /**
     * Add task_created
     *
     * @param \AppBundle\Entity\Task $task
     * @return User
     */
    public function addTaskCreated(\AppBundle\Entity\Task $task) {
        $this->tasks_created[] = $task;
        return $this;
    }

    /**
     * Remove task_created
     *
     * @param \AppBundle\Entity\Task $task
     */
    public function removeTaskCreated(\AppBundle\Entity\Task $task) {
        $this->tasks_created->removeElement($task);
    }

    /**
     * Get notes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotes() {
        return $this->notes;
    }

    /**
     * Add notes
     *
     * @param \AppBundle\Entity\TaskNote $note
     * @return User
     */
    public function addNote(\AppBundle\Entity\TaskNote $note) {
        $this->notes[] = $note;
        return $this;
    }

    /**
     * Remove note
     *
     * @param \AppBundle\Entity\TaskNote $note
     */
    public function removeNote(\AppBundle\Entity\TaskNote $note) {
        $this->notes->removeElement($note);
    }

    /**
     * Set recoveryCode
     *
     * @param string $recoveryCode
     *
     * @return User
     */
    public function setRecoveryCode($recoveryCode)
    {
        $this->recovery_code = $recoveryCode;

        return $this;
    }

    /**
     * Get recoveryCode
     *
     * @return string
     */
    public function getRecoveryCode()
    {
        return $this->recovery_code;
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTasksCreated()
    {
        return $this->tasks_created;
    }

    public function __construct() {
        $this->tasks_assigned = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tasks_created = new \Doctrine\Common\Collections\ArrayCollection();
        $this->proyect = new \Doctrine\Common\Collections\ArrayCollection();
        $this->notes = new \Doctrine\Common\Collections\ArrayCollection();
    }
}
