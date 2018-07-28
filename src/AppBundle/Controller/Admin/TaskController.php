<?php

namespace AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Util\AdminMenusIcon;
use AppBundle\Util\AdminMenusInit;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\TaskType;
use AppBundle\Entity\Task;
use AppBundle\Entity\Attached;
use AppBundle\Util\Helpers;
use AppBundle\Entity\TaskNote;

class TaskController extends Controller {

    public function tasksAction(Request $request) {
        $show_button_add_task = 1;
        $em = $this->getDoctrine()->getManager();
        $users_assigned = $em->getRepository('AppBundle:User')->findAll();
		$proyects = $em->getRepository('AppBundle:Proyect')->findAll();
        $role_admin = true;
        if($this->getUser()->getRol() == 'ROLE_USER'){
            if(count($this->getUser()->getProyect()) == 0){
                $show_button_add_task = 0;
				$proyects = array();
            }
			$proyects = $this->getUser()->getProyect();
            $role_admin = false;
        }
        $request->getSession()->set('left_menu', 'tasks_created');
        return $this->render('@App/Admin/task/tasks.html.twig', array(
            'show_button_add_task' => $show_button_add_task,
            'role_admin' => $role_admin,
            'users_assigned' => $users_assigned,
            'status' => $em->getRepository('AppBundle:State')->findAll(),
			'proyects' => $proyects
        ));
    }

    public function getAjaxTasksAction(Request $request) {
        $user_id = null;
        if($this->getUser() && $this->getUser()->getRol() == 'ROLE_USER')
            $user_id = $this->getUser()->getId();
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $search = $request->get('search');

        $where = '';
        $filter = $request->get('filter');
        $proyects = (isset($filter['proyect']))?$filter['proyect']:'';
        if($proyects != ''){
            $where.= ($where != '')?' AND ':'';
            $where.= "p.id IN (".implode(',',$proyects).")";
        }
        $priority = (isset($filter['priority']))?$filter['priority']:'';
        if($priority != ''){
            $where.= ($where != '')?' AND ':'';
            $where.= "t.priority = '".$priority."'";
        }
        $status = (isset($filter['states']))?$filter['states']:'';
        if($status != ''){
            $where.= ($where != '')?' AND ':'';
            $where.= "s.id IN (".implode(',',$status).")";
        }
        $user_created_tasks = (isset($filter['users_created_task']))?$filter['users_created_task']:'';
        if($user_created_tasks != ''){
            $where.= ($where != '')?' AND ':'';
            $where.= "u.id IN (".implode(',', $user_created_tasks).")";
        }
        $user_assigned_to = (isset($filter['users_assigned_to']))?$filter['users_assigned_to']:'';
        if($user_assigned_to != ''){
            $where.= ($where != '')?' AND ':'';
            $where.= "ua.id IN (".implode(',', $user_assigned_to).")";
        }
        $createdOn = (isset($filter['createdOn']))?$filter['createdOn']:'';
        if($createdOn != ''){
            $where.= ($where != '')?' AND ':'';
            $dates = explode('-', $createdOn);

            $string_start_date = Helpers::getDatetimeByString(trim($dates[0]),'m/d/Y', '/');
            $string_end_date = Helpers::getDatetimeByString(trim($dates[1]),'m/d/Y', '/');

            $start_date = $string_start_date['Y'].'-'.$string_start_date['m'].'-'.$string_start_date['d'];
            $end_date = $string_end_date['Y'].'-'.$string_end_date['m'].'-'.$string_end_date['d'];

            $where.="t.start_time between '".$start_date."' and '".$end_date."'";
        }
        $em = $this->getDoctrine()->getManager();
        $tasks = $em->getRepository('AppBundle:Task')->getTaskCreated($start, $length, $search['value'], $user_id, $where);
        $recordsFiltered = count($em->getRepository('AppBundle:Task')->getTaskCreated(0, 'all', $search['value'], $user_id, $where));
        $recordsTotal = $em->getRepository('AppBundle:Task')->getCountTaskCreated($user_id);
        $count = count($tasks);
        $adminMenus = $this->get('admin.menus');
        $translator = $this->get('translator');

        for ($i = 0; $i < $count; $i++) {
            $task_obj = $em->getRepository('AppBundle:Task')->findOneById($tasks[$i]['id']);
            $style = ($tasks[$i]['enabled'])?'normal;':'oblique;';
            $inactive = 'disabled';
            $menuInit = new AdminMenusInit($translator->trans('options', array(), 'admin'), $tasks[$i]['id']);
            if($tasks[$i]['state_id'] == 3){
                $menuInit->addModal('Completed Task', AdminMenusIcon::COMPLETETASK, 'completedInCreateTask');
                $menuInit->addModal('Disapprove Task', AdminMenusIcon::DISAPPROVETASK, 'disapproveInCreateTask');
            }
            if($tasks[$i]['state_id'] != 4){
                if($tasks[$i]['enabled'])
                    $menuInit->addMenu('Edit', AdminMenusIcon::EDIT, 'system_task_edit', array('id' => $tasks[$i]['id'], 'default' => 0));
            }
            if($tasks[$i]['enabled']){
                $inactive = '';
                $menuInit
                    ->addMenu('Details', AdminMenusIcon::SEARCH, null)
                    ->addMenu('Delete', AdminMenusIcon::TRASH, 'system_task_delete', array('id' => $tasks[$i]['id']));

                $links = $adminMenus->buildMenuInline($menuInit);
            }
            else{
                $links = '<b style="font-style:'.$style.'">INACTIVE</b>';
            }
           
            $tasks[$i]['actions'] = $links;
            $tasks[$i]['proyect'] = '<p style="font-style:'.$style.'">'.$tasks[$i]['proyect'].'</p>';
            $tasks[$i]['name'] = '<p style="font-style:'.$style.'">'.$tasks[$i]['name'].'</p>';
            $tasks[$i]['state'] = '<p style="font-style:'.$style.'">'.$tasks[$i]['state'].'</p>';
            $tasks[$i]['user_assigned'] = '<p style="font-style:'.$style.'">'.$tasks[$i]['user_assigned'].'</p>';
            $tasks[$i]['user_created'] = '<p style="font-style:'.$style.'">'.$tasks[$i]['user_created'].'</p>';
            $start_time = ($tasks[$i]['start_time'])?$tasks[$i]['start_time']->format('m-d-Y'):'';
            $tasks[$i]['start_time'] = '<p style="font-style:'.$style.'">'.$start_time.'</p>';
            $tasks[$i]['end_time'] = '<p style="font-style:'.$style.'">'.$tasks[$i]['end_time']->format('m-d-Y').'</p>';
            $tasks[$i]['priority'] = '<p style="font-style:'.$style.'">'.$tasks[$i]['priority'].'</p>';
            
            if($tasks[$i]['frequency_enable']){
                $tasks[$i]['frequency'] = $this->buildSelectFrequency($tasks[$i]['frequency_id']);
            }
            else{
                $tasks[$i]['frequency'] = '';
            }
            $description = $this->container->get('twig')->render('@App/Admin/_popover.html.twig', array(
                'description' => $tasks[$i]['description'],
                'inactive' => $inactive
            ));
            $tasks[$i]['description'] = $description;
        }
        $info = array();
        $info['draw'] = (int) $request->get('draw');
        $info['recordsTotal'] = (int) $recordsTotal;
        $info['recordsFiltered'] = (int) $recordsFiltered;
        $info['data'] = $tasks;

        return new \Symfony\Component\HttpFoundation\JsonResponse($info);
    }

    public function getAjaxTaskAction(Request $request) {
        $id = $request->get('id', false);
        $translator = $this->get('translator');
        if (!$id)
            return new Response($translator->trans('paramter.error', array(), 'admin'));

        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('AppBundle:Task')->findOneById($id);

        if (!$task)
            return new Response($translator->trans('info.nofound', array(), 'admin'), 200, array(
                'Content-Type' => 'text/html'
            ));

        $attached = $task->getAttached();
        $notes = $task->getNotes();

        return $this->render('@App/Admin/task/taskPreview.html.twig', array(
            'task' => $task,
            'attached' => $attached,
            'notes' => $notes
        ));
    }

    public function addAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $form = new TaskType();
        $task = new Task();
        $proyects_list = $em->getRepository('AppBundle:Proyect')->findAll();
        if($this->getUser()->getRol() == 'ROLE_USER'){
            $proyects_list = $this->getUser()->getProyect();
        }
        $form->setProyects(Helpers::getArrayProyect($proyects_list));
        $form = $this->createForm($form, $task);

        if ($request->getMethod() == 'POST') {
            $data = $request->get('appbundle_task');
            $endTime = Helpers::getObjectDateTime($data['end_time'], '-');
            $startTime = Helpers::getObjectDateTime($data['start_time'], '-');
            $proyect = $em->getRepository('AppBundle:Proyect')->findOneById($data['proyect']);
            $task->setProyect($proyect);
            $form->handleRequest($request);
	    
            $task->setFrequencyEnable(1);
            $user = $em->getRepository('AppBundle:User')->findOneById($this->getUser()->getId());
            $task->setStartTime($startTime);
            $task->setEndTime($endTime);
            $task->setUserCreatedTask($user);
            $status = 2;
            if(!$task->getUser()){
		$status = 1;
                $task->setStartTime(null);
	    }

            $status_obj = $em->getRepository('AppBundle:State')->findOneById($status);
            $task->setState($status_obj);
            $status_obj->addTask($task);
            $em->persist($status_obj);
            $em->persist($task);
            $name = $task->getName();
            $em->flush();

            if($status == 2){
                $message = Helpers::messageNewTask($this->container, $task, $proyect->getName());
                $this->container->get('mailer')->send($message);
            }

            $files = (count($request->files)>0)?$request->files->get('file'):array();
            foreach($files as $file){
                $attached = new Attached();
                $attached->setName($file->getClientOriginalName());
                $attached->setAttached($file);
                $attached->setTask($task);
                $path = $this->container->getParameter('web_dir') . 'img/attached/';
                $attached->uploadFile($path);
                $em->persist($attached);
                $task->addAttached($attached);
            }
            $em->flush();

            $this->get('session')->getFlashBag()->add('info', 'Task <b>'.$name.'</b> has been added.');
            return $this->redirect($this->generateUrl('system_tasks_created'));
        }
        return $this->render('@App/Admin/task/form.html.twig', array(
            'form' => $form->createView(),
            'action' => 'New',
        ));
    }

    public function editAction(Request $request, $id, $default=0) {
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('AppBundle:Task')->findOneById($id);

        if($this->getUser()->getRol() == 'ROLE_USER' && $task->getUserCreatedTask()->getId() != $this->getUser()->getId())
            return Helpers::error404($this, 'You do not have permission to edit this task', 'Back to list tasks', 'fa-tasks', $this->generateUrl('system_tasks_created'));
        if (!$task)
            return Helpers::error404($this, 'Task not found', 'Back to list tasks', 'fa-tasks', $this->generateUrl('system_tasks_created'));
        $attached = $task->getAttached();
        $taskType = new TaskType();
        $lastStartTime = $task->getStartTime();
        $task->setStartTime(($task->getStartTime())?$task->getStartTime()->format('m-d-Y'):'');
        $task->setEndTime($task->getEndTime()->format('m-d-Y'));

        $proyects_list = $em->getRepository('AppBundle:Proyect')->findAll();
        if($this->getUser()->getRol() == 'ROLE_USER'){
            $proyects_list = $this->getUser()->getProyect();
        }

        $lastUser = $task->getUser();
        $taskType->setProyects(Helpers::getArrayProyect($proyects_list));

        $form = $this->createForm($taskType, $task);

        if ($request->getMethod() == 'POST') {
            $data = $request->get('appbundle_task');

            $form->handleRequest($request);
            $data = $request->get('appbundle_task');
            $endTime = Helpers::getObjectDateTime($data['end_time'], '-');
            $startTime = Helpers::getObjectDateTime($data['start_time'], '-');
            $task->setEndTime($endTime);
            $task->setStartTime($startTime);
            
            $name = $task->getName();
            $dir = $this->container->getParameter('web_dir');

            $names_attached_to_delete = explode(',', $request->get('name-attached-removed'));

            foreach($names_attached_to_delete as $attached_delete){
                $obj_attached = $em->getRepository('AppBundle:Attached')->findOneByAttached($attached_delete);
                if($obj_attached){
                    $task->removeAttached($obj_attached);
                    $em->remove($obj_attached);
                    @unlink($dir.'img/attached/'.$attached_delete);
                }
            }
            $proyect = $em->getRepository('AppBundle:Proyect')->findOneById($task->getProyect());
            if($lastStartTime == null && $task->getUser() != null)
                $task->setStartTime($startTime);

            if($task->getUser() != null && $lastUser == null && ($task->getState()->getId() == 1 ||  $task->getState()->getId() == 2 || $task->getState()->getId() == 3)){//new task
                $status = $em->getRepository('AppBundle:State')->findOneById(2);
                $task->setState($status);

                $message = Helpers::messageNewTask($this->container, $task, $proyect->getName());
                $this->container->get('mailer')->send($message);
            }
            else if($task->getUser() == null && $lastUser != null){ //que deje la tarea sin usuario
                $message = Helpers::messageRemoveTask($this->container,$lastUser,$task->getName());
                $this->container->get('mailer')->send($message);
            }
            else if($task->getUser() != null && $lastUser != null && $task->getUser()->getId() != $lastUser->getId()){//que cambie de un usuario a otro
                $message = Helpers::messageNewTask($this->container, $task, $proyect->getName());
                $this->container->get('mailer')->send($message);

                $message = Helpers::messageRemoveTask($this->container,$lastUser,$task->getName());
                $this->container->get('mailer')->send($message);
            }
            else if($task->getUser() != null && $lastUser != null && $task->getUser()->getId() == $lastUser->getId()){//tarea editada sin cambiar usuario
                $message = Helpers::messageNewTask($this->container, $task, $proyect->getName(), 'The task has been edit');
                $this->container->get('mailer')->send($message);
            }

            $em->persist($task);
            $em->flush();

            if($data['notes']!=''){
               $note = new TaskNote();
               $note->setTask($task);
               $note->setCreatedOn(new \DateTime('now'));
               $note->setNote($data['notes']);
               $note->setUser($this->getUser());
               $em->persist($note);
               $em->flush();
            }

            $files = (count($request->files)>0)?$request->files->get('file'):array();
            foreach($files as $file){
                $attached = new Attached();
                $attached->setName($file->getClientOriginalName());
                $attached->setAttached($file);
                $attached->setTask($task);
                $path = $dir . 'img/attached/';
                $attached->uploadFile($path);
                $em->persist($attached);
                $task->addAttached($attached);
            }
            $em->flush();

            $this->get('session')->getFlashBag()->add('info', 'Task <b>'.$name.'</b> has been edited.');
            if($default == 0)
                return $this->redirect($this->generateUrl('system_tasks_created'));
            else
                return $this->redirect($this->generateUrl('dashboard'));
        }


        return $this->render('@App/Admin/task/form.html.twig', array(
            'form' => $form->createView(),
            'action' => 'Edit',
            'attached' => $attached,
            'id'=> $id,
            'http_host' => 'http://'.$_SERVER['HTTP_HOST'],
            'default' => $default
        ));
    }
    
    public function deleteAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('AppBundle:Task')->findOneById($id);

        if (!$task)
            return Helpers::error404($this, 'Task not found', 'Back Tasks', 'fa fa-tasks fa-fw', $this->generateUrl('system_tasks_created'));

        $dir_image_web = $this->container->getParameter('web_dir_attached');
        $attacheds = $task->getAttached();
        foreach($attacheds as $attached){
            $attached_repeat = $em->getRepository('AppBundle:Attached')->getRepeatAttached($attached->getAttached(),$id);
            if(count($attached_repeat) == 0){
                $file = $dir_image_web . $attached->getAttached();
                if (file_exists($file)){
                    @unlink($file);
                }
            }
        }        
        
        $task_name = $task->getName();
        $em->remove($task);
        $em->flush();

        $this->get('session')->getFlashBag()->add('info', 'Task <b>'.$task_name.'</b> has been deleted.');
        return $this->redirect($this->generateUrl('system_tasks_created'));
    }

    public function completeTaskAction(Request $request){
        $task_id = $request->get('id', false);
        if (!$task_id)
            return new Response(false);

        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('AppBundle:Task')->findOneById($task_id);
        $project = $em->getRepository('AppBundle:Proyect')->findOneById($task->getProyect());
        if (!$task)
            return new Response(false);
        $state_complete = $em->getRepository('AppBundle:State')->findOneById(4);
        $task->setState($state_complete);

        $em->persist($task);
        $em->flush();

        $message = Helpers::messageCompletetask($this->container, $task, $project);
        $this->container->get('mailer')->send($message);

        $notes = $request->get('notes', '');
        if($notes != ''){
            $note = new TaskNote();
            $note->setTask($task);
            $note->setCreatedOn(new \DateTime('now'));
            $note->setNote($notes);
            $note->setUser($this->getUser());
            $em->persist($note);
            $em->flush();
        }
        $this->get('session')->getFlashBag()->add('info', 'Task <b>'.$task->getName().'</b> has been completed.');
        return new \Symfony\Component\HttpFoundation\JsonResponse(array('response' => true));
    }

    public function disapproveTaskAction(Request $request){
        $task_id = $request->get('id', false);
        if (!$task_id)
            return new Response(false);

        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('AppBundle:Task')->findOneById($task_id);
        $proyect = $em->getRepository('AppBundle:Proyect')->findOneById($task->getProyect());
        if (!$task)
            return new Response(false);
        $state_pending = $em->getRepository('AppBundle:State')->findOneById(2);
        $task->setState($state_pending);

        $em->persist($task);
        $em->flush();

        $message = Helpers::messageDesapproveTask($this->container, $task,$proyect);
        $this->container->get('mailer')->send($message);

        $notes = $request->get('notes', '');
        if($notes != ''){
            $note = new TaskNote();
            $note->setTask($task);
            $note->setCreatedOn(new \DateTime('now'));
            $note->setNote($notes);
            $note->setUser($this->getUser());
            $em->persist($note);
            $em->flush();
        }
        //$this->get('session')->getFlashBag()->add('info', 'Task <b>'.$task->getName().'</b> has been disapproved.');
        return new \Symfony\Component\HttpFoundation\JsonResponse(array('response' => true));
    }
    
    public function changeFrequencyAction(Request $request){
        $frequency_id = $request->get('frequency', false);
        $task_id = $request->get('task', false);

        if (!$task_id || !$frequency_id)
            return new \Symfony\Component\HttpFoundation\JsonResponse(array('response' => false));

        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('AppBundle:Task')->findOneById($task_id);
        $frequency = $em->getRepository('AppBundle:Frequency')->findOneById($frequency_id);
        if (!$task)
            return new \Symfony\Component\HttpFoundation\JsonResponse(array('response' => false));

        $today = new \DateTime('now');
        $task->setFrequencyDate($today);
        
        $task->setFrequency($frequency);

        $em->persist($task);
        $em->flush();
        return new \Symfony\Component\HttpFoundation\JsonResponse(array('response' => true));
    }


    //----------------------MY TASK------------------------------------------------------------------------------------------------------------------------------
    public function tasksAssignedAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $users_assigned = $em->getRepository('AppBundle:User')->findAll();
        $role_admin = false;
        $tasks_proyect = $em->getRepository('AppBundle:Task')->getProyectWithUserAssignedInTask($this->getUser()->getId());
        $proyects = array();
        foreach($tasks_proyect as $obj)
            $proyects[] = $obj['proyect'];
        $request->getSession()->set('left_menu', 'tasks_assigned');
        return $this->render('@App/Admin/task/assigned/tasks.html.twig', array(
            'role_admin' => $role_admin,
            'users_assigned' => $users_assigned,
            'status' => $em->getRepository('AppBundle:State')->findAll(),
            'proyects' => $proyects
        ));
    }

    public function getAjaxTasksAssignedAction(Request $request) {
        $user_id = $this->getUser()->getId();
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $search = $request->get('search');

        $where = '';
        $filter = $request->get('filter');
        $proyects = (isset($filter['proyect']))?$filter['proyect']:'';
        if($proyects != ''){
            $where.= ($where != '')?' AND ':'';
            $where.= "p.id IN (".implode(',',$proyects).")";
        }
        $priority = (isset($filter['priority']))?$filter['priority']:'';
        if($priority != ''){
            $where.= ($where != '')?' AND ':'';
            $where.= "t.priority = '".$priority."'";
        }
        $status = (isset($filter['states']))?$filter['states']:'';
        if($status != ''){
            $where.= ($where != '')?' AND ':'';
            $where.= "s.id IN (".implode(',',$status).")";
        }
        $user_created_tasks = (isset($filter['users_created_task']))?$filter['users_created_task']:'';
        if($user_created_tasks != ''){
            $where.= ($where != '')?' AND ':'';
            $where.= "u.id IN (".implode(',', $user_created_tasks).")";
        }
        $user_assigned_to = (isset($filter['users_assigned_to']))?$filter['users_assigned_to']:'';
        if($user_assigned_to != ''){
            $where.= ($where != '')?' AND ':'';
            $where.= "ua.id IN (".implode(',', $user_assigned_to).")";
        }
        $createdOn = (isset($filter['createdOn']))?$filter['createdOn']:'';
        if($createdOn != ''){
            $where.= ($where != '')?' AND ':'';
            $dates = explode('-', $createdOn);

            $string_start_date = Helpers::getDatetimeByString(trim($dates[0]),'m/d/Y', '/');
            $string_end_date = Helpers::getDatetimeByString(trim($dates[1]),'m/d/Y', '/');

            $start_date = $string_start_date['Y'].'-'.$string_start_date['m'].'-'.$string_start_date['d'];
            $end_date = $string_end_date['Y'].'-'.$string_end_date['m'].'-'.$string_end_date['d'];

            $where.="t.start_time between '".$start_date."' and '".$end_date."'";
        }

        $em = $this->getDoctrine()->getManager();
        $tasks = $em->getRepository('AppBundle:Task')->getTaskAssigned($start, $length, $search['value'], $user_id, $where);
        $recordsFiltered = count($em->getRepository('AppBundle:Task')->getTaskAssigned(0, 'all', $search['value'], $user_id, $where));
        $recordsTotal = $em->getRepository('AppBundle:Task')->getCountTaskAssigned($user_id);
        $count = count($tasks);
        $adminMenus = $this->get('admin.menus');
        $translator = $this->get('translator');

        for ($i = 0; $i < $count; $i++) {
            $task_obj = $em->getRepository('AppBundle:Task')->findOneById($tasks[$i]['id']);
            $menuInit = new AdminMenusInit($translator->trans('options', array(), 'admin'), $tasks[$i]['id']);
            $style = ($tasks[$i]['enabled'])?'normal;':'oblique;';
            $inactive = 'disabled';
            $menuInit = new AdminMenusInit($translator->trans('options', array(), 'admin'), $tasks[$i]['id']);
            if($tasks[$i]['state_id'] != 4 && $tasks[$i]['state_id'] != 3){
                if($tasks[$i]['enabled'])
                    $menuInit->addMenu('Resolved Task', AdminMenusIcon::CHECK, 'system_task_assigned_resolved', array('id' => $tasks[$i]['id'], 'default' => 0));
            }
            if($tasks[$i]['enabled']){
                $inactive = '';
                $menuInit
                    ->addMenu('Details', AdminMenusIcon::SEARCH, null);
                $links = $adminMenus->buildMenuInline($menuInit);
            }
            else{
                $links = '<b style="font-style:'.$style.'">INACTIVE</b>';
            }

            $tasks[$i]['proyect'] = '<p style="font-style:'.$style.'">'.$tasks[$i]['proyect'].'</p>';
            $tasks[$i]['name'] = '<p style="font-style:'.$style.'">'.$tasks[$i]['name'].'</p>';
            $tasks[$i]['state'] = '<p style="font-style:'.$style.'">'.$tasks[$i]['state'].'</p>';
            $tasks[$i]['user_assigned'] = '<p style="font-style:'.$style.'">'.$tasks[$i]['user_assigned'].'</p>';
            $tasks[$i]['user_created'] = '<p style="font-style:'.$style.'">'.$tasks[$i]['user_created'].'</p>';
            $start_time = ($tasks[$i]['start_time'])?$tasks[$i]['start_time']->format('m-d-Y'):'';
            $tasks[$i]['start_time'] = '<p style="font-style:'.$style.'">'.$start_time.'</p>';
            $tasks[$i]['end_time'] = '<p style="font-style:'.$style.'">'.$tasks[$i]['end_time']->format('m-d-Y').'</p>';
            $tasks[$i]['priority'] = '<p style="font-style:'.$style.'">'.$tasks[$i]['priority'].'</p>';
            $description = $this->container->get('twig')->render('@App/Admin/_popover.html.twig', array(
                'description' => $tasks[$i]['description'],
                'inactive' => $inactive
            ));
            $tasks[$i]['description'] = $description;
            $tasks[$i]['actions'] = $links;
        }
        $info = array();
        $info['draw'] = (int) $request->get('draw');
        $info['recordsTotal'] = (int) $recordsTotal;
        $info['recordsFiltered'] = (int) $recordsFiltered;
        $info['data'] = $tasks;

        return new \Symfony\Component\HttpFoundation\JsonResponse($info);
    }

    public function ajaxResolvedTaskAction(Request $request){
        $id = $request->get('id', false);
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('AppBundle:Task')->findOneById($id);

        if (!$task)
            return Helpers::error404($this, 'The task does not exist', 'Return to dashboard', 'fa-tasks', $this->generateUrl('dashboard'));

        $state = $em->getRepository('AppBundle:State')->findOneById(3);

        $task->setState($state);
        $em->persist($task);
        $em->flush();

        $project = $em->getRepository('AppBundle:proyect')->findOneById($task->getProyect());
        $message = Helpers::messageResolvedTask($this->container, $task, $project);
        $this->container->get('mailer')->send($message);

        return new \Symfony\Component\HttpFoundation\JsonResponse(true);
    }

    public function resolvedMyTaskAction(Request $request, $id, $default = 0) {
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('AppBundle:Task')->findOneById($id);

        if($this->getUser()->getRol() == 'ROLE_USER' && $task->getUser()->getId() != $this->getUser()->getId())
            return Helpers::error404($this, 'You do not have permission to resolved this task', 'Return to list tasks', 'fa-tasks', $this->generateUrl('system_tasks_assigned'));
        if (!$task)
            return Helpers::error404($this, 'The task does not exist', 'Return to list tasks', 'fa-tasks', $this->generateUrl('system_tasks_assigned'));
        $attached = $task->getAttached();

        if ($request->getMethod() == 'POST') {

            $data = $request->get('appbundle_task');

            $name = $task->getName();
            $dir = $this->container->getParameter('web_dir');

            $names_attached_to_delete = explode(',', $request->get('name-attached-removed'));

            foreach($names_attached_to_delete as $attached_delete){
                $obj_attached = $em->getRepository('AppBundle:Attached')->findOneByAttached($attached_delete);
                if($obj_attached){
                    $task->removeAttached($obj_attached);
                    $em->remove($obj_attached);
                    @unlink($dir.'img/attached/'.$attached_delete);
                }
            }
            $state = $em->getRepository('AppBundle:State')->findOneById(3);
            $task->setState($state);
            $em->persist($task);
            $em->flush();

            $project = $em->getRepository('AppBundle:proyect')->findOneById($task->getProyect());
            $message = Helpers::messageResolvedTask($this->container, $task, $project);
            $this->container->get('mailer')->send($message);

            if($data['notes']!=''){
                $note = new TaskNote();
                $note->setTask($task);
                $note->setCreatedOn(new \DateTime('now'));
                $note->setNote($data['notes']);
                $note->setUser($this->getUser());
                $em->persist($note);
                $em->flush();
            }

            $files = (count($request->files)>0)?$request->files->get('file'):array();
            foreach($files as $file){
                $attached = new Attached();
                $attached->setName($file->getClientOriginalName());
                $attached->setAttached($file);
                $attached->setTask($task);
                $path = $dir . 'img/attached/';
                $attached->uploadFile($path);
                $em->persist($attached);
                $task->addAttached($attached);
            }
            $em->flush();

            $this->get('session')->getFlashBag()->add('info', 'the task  <b>'.$name.'</b> has been resolved.');

            if($default == 0)
                return $this->redirect($this->generateUrl('system_tasks_assigned'));
            else
                return $this->redirect($this->generateUrl('dashboard'));
        }


        return $this->render('@App/Admin/task/assigned/form.html.twig', array(
            'action' => 'Edit',
            'attached' => $attached,
            'id'=> $id,
            'http_host' => 'http://'.$_SERVER['HTTP_HOST'],
            'default' => $default
        ));
    }

    //---------------------NOTES--------------------------------------------------------------------------------------------------------------------------------
    public function getAjaxTaskNotesAction(Request $request) {
        $task_id = $request->get('task_id', 0);
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $search = $request->get('search');

        $em = $this->getDoctrine()->getManager();
        $notes = $em->getRepository('AppBundle:TaskNote')->getTaskNotes($start, $length, $search['value'], $task_id);
        $recordsFiltered = count($em->getRepository('AppBundle:TaskNote')->getTaskNotes(0, 'all', $search['value'], $task_id));
        $recordsTotal = $em->getRepository('AppBundle:TaskNote')->getCount($task_id);
        $count = count($notes);

        for ($i = 0; $i < $count; $i++) {
            $notes[$i]['createdOn'] = $notes[$i]['createdOn']->format('m-d-Y');
        }

        $info = array();
        $info['draw'] = (int) $request->get('draw');
        $info['recordsTotal'] = (int) $recordsTotal;
        $info['recordsFiltered'] = (int) $recordsFiltered;
        $info['data'] = $notes;

        return new \Symfony\Component\HttpFoundation\JsonResponse($info);
    }
    
    private function buildSelectFrequency($id){
        $em = $this->getDoctrine()->getManager();
        $frequencies = $em->getRepository('AppBundle:Frequency')->findAll();
        
        $select = "<div class='dropdown'>";
        $button = "<button class='btn btn-default btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
            <i class='fa fa-fw fa fa-circle text-default'></i> Disabled <span class='caret'></span>
        </button>";
        $li = '';
        $li_default = "<ul class='dropdown-menu'> <li class='disabled'><a data-id = '-1' href='javascript: void(0)'><i class='fa fa-fw fa-circle text-default  m-r-1'></i> Disabled</a></li>";
        $flag = false;
        foreach($frequencies as $frequency){
            $disabled = '';
            if($frequency->getId() == $id){
                $disabled = "class='disabled'";
                $flag = true;
                $li_default = "<ul class='dropdown-menu'> <li><a data-id = '-1' href='javascript: void(0)'><i class='fa fa-fw fa-circle text-default  m-r-1'></i> Disabled</a></li>";
                $button = "<button class='btn btn-default btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                        <i class='fa fa-fw fa fa-circle ".$frequency->getClassCss()."'></i>". $frequency->getName()." <span class='caret'></span></button>";
            }
            $li.= "<li ".$disabled."><a data-id='".$frequency->getId()."' href='javascript: void(0)'><i class='fa fa-fw fa-circle ".$frequency->getClassCss()." m-r-1'></i> ".$frequency->getName()." </a></li>";
        }
        $bottom = "</ul></div>";
        $dropdown = $select.$button.$li_default.$li.$bottom;
        return $dropdown;
    }

}

