<?php

namespace AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Util\AdminMenusIcon;
use AppBundle\Util\AdminMenusInit;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\ProyectType;
use AppBundle\Entity\Proyect;
use AppBundle\Util\Helpers;

class ProyectController extends Controller {

    public function proyectAction(Request $request) {
        $request->getSession()->set('left_menu', 'proyects');
        return $this->render('@App/Admin/proyect/proyects.html.twig');
    }

    public function getAjaxProyectsAction(Request $request) {
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $search = $request->get('search');
        $em = $this->getDoctrine()->getManager();
        $proyects = $em->getRepository('AppBundle:Proyect')->getProyects($start, $length, $search['value']);
        $recordsFiltered = count($em->getRepository('AppBundle:Proyect')->getProyects(0, 'all', $search['value']));
        $recordsTotal = $em->getRepository('AppBundle:Proyect')->getCount();
        $count = count($proyects);
        $adminMenus = $this->get('admin.menus');
        $translator = $this->get('translator');

        for ($i = 0; $i < $count; $i++) {
            $menuInit = new AdminMenusInit($translator->trans('options', array(), 'admin'), $proyects[$i]['id']);
            $menuInit->addMenu('Edit', AdminMenusIcon::EDIT, 'admin_proyect_edit', array('id' => $proyects[$i]['id']));
            $menuInit
                ->addMenu('Details', AdminMenusIcon::SEARCH, null);

            $proyects[$i]['created_time'] = $proyects[$i]['created_time']->format('m/d/Y');
            $links = $adminMenus->buildMenuInline($menuInit);
            $proyects[$i]['actions'] = $links;
            $state = ($proyects[$i]['enabled'])?'0':'1';
            $id_changeStatus = 'changeStatus-'.$proyects[$i]['id'].'-'.$state;
            $proyects[$i]['enabled_disable'] = ($proyects[$i]['enabled'])? "<span id='".$id_changeStatus."' style='cursor: pointer' class='Enabled-Disable fa ".AdminMenusIcon::DISABLE." fa-1x'></span>" : "<span id='".$id_changeStatus."'  style='cursor: pointer' class='Enabled-Disable fa ".AdminMenusIcon::ENABLE." fa-1x'></span>";
            $proyects[$i]['enabled'] = ($proyects[$i]['enabled'])?'Enabled':'Disabled';
        }
        $info = array();
        $info['draw'] = (int) $request->get('draw');
        $info['recordsTotal'] = (int) $recordsTotal;
        $info['recordsFiltered'] = (int) $recordsFiltered;
        $info['data'] = $proyects;

        return new \Symfony\Component\HttpFoundation\JsonResponse($info);
    }

    public function getAjaxProyectAction(Request $request) {
        $id = $request->get('id', false);
        $translator = $this->get('translator');
        if (!$id)
            return new Response($translator->trans('paramter.error', array(), 'admin'));

        $em = $this->getDoctrine()->getManager();
        $proyect = $em->getRepository('AppBundle:Proyect')->findOneById($id);

        if (!$proyect)
            return new Response($translator->trans('info.nofound', array(), 'admin'), 200, array(
                'Content-Type' => 'text/html'
            ));

        $resumeTask = $em->getRepository('AppBundle:Task')->getResumeCountTaskByStatus($proyect->getId());
        $amountTask =  $em->getRepository('AppBundle:Task')->getCountTaskByProyectAndStatusAndUserCreatedtask($proyect->getId());

        return $this->render('@App/Admin/proyect/proyectPreview.html.twig', array(
            'proyect' => $proyect,
            'resumeTask' => $resumeTask,
            'amountTask' => $amountTask
        ));
    }

    public function addAction(Request $request) {
        $form = new ProyectType();
        $proyect = new Proyect();
        $form = $this->createForm($form, $proyect);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            $em = $this->getDoctrine()->getManager();
            if ($form->isValid()) {
                $userAssigned = $request->get('appbundle_proyect_extra');

                if($userAssigned['user_assigned'] != ''){
                    $list_users = explode(',', $userAssigned['user_assigned']);
                    for($i=0; $i<count($list_users); $i++){
                        $object_user = $em->getRepository('AppBundle:User')->getUserByIdObject($list_users[$i]);
                        if($object_user)
                            $proyect->addUser($object_user);
                    }
                }

                $users_admin = $em->getRepository('AppBundle:User')->getUsersByUserRole();
                foreach($users_admin as $obj){
                    $proyect->addUser($obj);
                }

                $proyect->setEnabled(true);
                $date = new \DateTime('now');
                $proyect->setCreatedTime($date);
                $em->persist($proyect);

                $name = $proyect->getName();
                $em->flush();
                $this->get('session')->getFlashBag()->add('info', 'Project <b>'.$name.'</b> has been added.');
                return $this->redirect($this->generateUrl('admin_proyects'));
            }
        }
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:User')->getUsersToAssignProyect();

        return $this->render('@App/Admin/proyect/form.html.twig', array(
            'form' => $form->createView(),
            'action' => 'New',
            'users' => $users
        ));
    }

    public function editAction(Request $request, $id) {
        $request->getSession()->set('left_menu', 'proyects');
        $em = $this->getDoctrine()->getManager();
        $proyect = $em->getRepository('AppBundle:Proyect')->findOneById($id);
        if (!$proyect)
            return Helpers::error404($this, 'Project not found', 'Back to list projects', 'fa-bookmark', $this->generateUrl('admin_proyects'));

        $users = $proyect->getUsers();
        $proyectType = new ProyectType();
        $form = $this->createForm($proyectType, $proyect);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            $em = $this->getDoctrine()->getManager();
            if ($form->isValid()) {

                foreach($users as $obj)
                    $proyect->removeUser($obj);
                $em->persist($proyect);
                $em->flush();

                $userAssigned = $request->get('appbundle_proyect_extra');
                if($userAssigned['user_assigned'] != ''){
                    $list_users = explode(',', $userAssigned['user_assigned']);
                    for($i=0; $i<count($list_users); $i++){
                        $object_user = $em->getRepository('AppBundle:User')->getUserByIdObject($list_users[$i]);
                        if($object_user)
                            $proyect->addUser($object_user);
                    }
                }

                $users_admin = $em->getRepository('AppBundle:User')->getUsersByUserRole();
                foreach($users_admin as $obj){
                    $proyect->addUser($obj);
                }

                $em->persist($proyect);
                $name = $proyect->getName();
                $em->flush();
                $this->get('session')->getFlashBag()->add('info', 'Project <b>'.$name.'</b> has been edited.');
                return $this->redirect($this->generateUrl('admin_proyects'));
            }
        }

        $user_string = '';
        foreach($users as $item){
            if($item->getRol() != 'ROLE_ADMIN' ){
                if($user_string == '')
                    $user_string = $item->getId();
                else{
                    $temp = ','.$item->getId();
                    $user_string.=$temp;
                }
            }
        }

        $users_proyect = $em->getRepository('AppBundle:User')->getUsersToAssignProyect();

        return $this->render('@App/Admin/proyect/form.html.twig', array(
            'form' => $form->createView(),
            'action' => 'Edit',
            'usersIds' => $user_string,
            'usersList' =>$users,
            'id'=> $id,
            'users'=>$users_proyect
        ));
    }

    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $proyect = $em->getRepository('AppBundle:Proyect')->findOneById($id);

        if (!$proyect)
            return Helpers::error404($this, 'Project not found', 'Back to list projects', 'fa-bookmark', $this->generateUrl('admin_proyects'));


        $name = $proyect->getName();
        $em->remove($proyect);
        $em->flush();

        $this->get('session')->getFlashBag()->add('info', 'Project <b>'.$name.'</b> has been deleted.');
        return $this->redirect($this->generateUrl('admin_proyects'));
    }

    public function getAjaxProyectChangeStatusAction(Request $request){
        $id = $request->get('id', false);
        $translator = $this->get('translator');
        if (!$id)
            return new Response($translator->trans('paramter.error', array(), 'admin'));

        $em = $this->getDoctrine()->getManager();
        $proyect = $em->getRepository('AppBundle:Proyect')->findOneById($id);

        if (!$proyect)
            return new Response($translator->trans('info.nofound', array(), 'admin'), 200, array(
                'Content-Type' => 'text/html'
            ));
        $resumeTask = $em->getRepository('AppBundle:Task')->getResumeCountTaskByStatus($proyect->getId());
        $amountTask =  $em->getRepository('AppBundle:Task')->getCountTaskByProyectAndStatusAndUserCreatedtask($proyect->getId());

        return $this->render('@App/Admin/proyect/proyectChangeStatusPreview.html.twig', array(
            'proyect' => $proyect,
            'resumeTask' => $resumeTask,
            'amountTask' => $amountTask
        ));
    }

    public function changeStatusProyectAction(Request $request){
        $id = $request->get('id', false);
        $status = $request->get('status', false);
        $translator = $this->get('translator');
        if (!$id)
            return new Response($translator->trans('paramter.error', array(), 'admin'));

        $em = $this->getDoctrine()->getManager();
        $proyect = $em->getRepository('AppBundle:Proyect')->findOneById($id);
        if (!$proyect)
            return new Response($translator->trans('info.nofound', array(), 'admin'), 200, array(
                'Content-Type' => 'text/html'
            ));

        $proyect->setEnabled($status);
        $em->persist($proyect);
        $em->flush();
        $name = $proyect->getName();

        $status_name = 'Enabled';
        if(!$status)
            $status_name = 'Disabled';

        $this->get('session')->getFlashBag()->add('info', 'Project <b>'.$name.'</b> has been '.$status_name);
        return $this->redirect($this->generateUrl('admin_proyects'));
    }
}

