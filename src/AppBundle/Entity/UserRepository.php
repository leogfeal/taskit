<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository implements UserProviderInterface {

    public function getCount() {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT COUNT (u)
            FROM AppBundle:User u
        ');
        return $query->getSingleScalarResult();
    }

    public function getUsers($start = 0, $length = 10, $search) {
        $searchDQL = "";
        if ($search) {
            $searchDQL .= "WHERE u.username LIKE '%$search%'
                OR u.email LIKE '%$search%'
                OR u.name LIKE '%$search%'
                OR u.rol LIKE '%$search%'
            ";
        }
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT u.username, u.email, u.name, u.phone, u.locked, u.id, u.rol
            FROM AppBundle:User u
            ' . $searchDQL . '
            ORDER BY u.name ASC
        ');
        $query->setFirstResult($start);
        if ($length != 'all')
            $query->setMaxResults($length);

        return $query->getArrayResult();
    }

    public function getUsersToAssignProyect() {

        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT u.username, u.email, u.name, u.phone, u.locked, u.id, u.rol
            FROM AppBundle:User u
            where u.rol = :rol
            ORDER BY u.name ASC
        ');
        $query->setParameter('rol', 'ROLE_USER');

        return $query->getArrayResult();
    }

    public function getUserById($id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT u.name, u.username, u.phone, u.email, u.address, u.rol
            FROM AppBundle:User u
            WHERE u.id = :id
        ');
        $query->setParameter('id', $id);
        return $query->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public function getUserByIdObject($id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT u
            FROM AppBundle:User u
            WHERE u.id = :id
        ');
        $query->setParameter('id', $id);
        return $query->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
    }

    public function getUsersByUserRole($rol = 'ROLE_ADMIN'){
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT u
            FROM AppBundle:User u
            WHERE u.rol = :rol
        ');

        $query->setParameter('rol', $rol);
        return $query->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
    }


    public function loadUserByUsername($username) {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT u
            FROM AppBundle:User u
            WHERE u.username = :username
        ');
        $query->setParameter('username', $username);
        return $query->getOneOrNullResult();
    }

    public function refreshUser(UserInterface $user) {
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class) {
        return $class === 'AppBundle\Entity\User';
    }

    public function checkEmailForgetPswd($email, $hydrate = \Doctrine\ORM\Query::HYDRATE_ARRAY)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT u
            FROM AppBundle:User u
            WHERE u.email = :email
        ');
        $query->setParameter('email', $email);
        return $query->getOneOrNullResult($hydrate);
    }

}