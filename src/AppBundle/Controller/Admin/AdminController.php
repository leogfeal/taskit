<?php

namespace AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller {

    public function dashboardAction(Request $request) {
        $request->getSession()->set('left_menu', 'main');
        if(!$this->getUser())
            return $this->redirect($this->generateUrl('login'));
        $em = $this->getDoctrine()->getManager();
//----------ZONE INFORMATION------------------------------------------------------------
        $information = array();
        $information['show_proyect'] = true;
        $information['proyects_amount_array'] = $this->getProyectByStatus();
        $information['amount_all_proyects'] = count($this->getUser()->getProyect());
        if($information['amount_all_proyects'] == 0)
            $information['show_proyect'] = false;

        $information['show_chart_task'] = true;

        $userId = $this->getUser()->getId();
        if($this->getUser()->getRol() == 'ROLE_ADMIN')
            $userId = null;

        $information['task_amount_array'] = $em->getRepository('AppBundle:Task')->getResumeCountTaskByStatus(null, $userId, 1);
        $information['amount_all_tasks'] = $em->getRepository('AppBundle:Task')->getCountTaskByProyectAndStatusAndUserCreatedtask(null, null, $userId, 1);
        if($information['amount_all_tasks'] == 0)
            $information['show_chart_task'] = false;

        $information['my_task_array'] = $em->getRepository('AppBundle:Task')->getResumeCountMyTaskByStatus(null, $this->getUser()->getId(), 1);
        $amount_my_task = 0;
        foreach($information['my_task_array'] as $task){
            $amount_my_task+= $task['amount'];
        }
        $information['amount_my_task'] = $amount_my_task;
//----------END ZONE INFORMATION--------------------------------------------------------
//----------TAB MANAGE-------------------------------------------------------------------
        $manage = array();
        $manage['proyects'] = $this->getProyectsOfTabManage();
        $manage['task_ready_for_test'] = $this->getTaskReadyForTestOfTabManage();
        $ready_for_test = $em->getRepository('AppBundle:State')->findOneById(3);
        $manage['color_ready_for_test'] = $ready_for_test->getColor();
//----------END TAB MANAGE---------------------------------------------------------------
//----------TAB MY TASK -----------------------------------------------------------------
        $myTask = array();
        $myTask['task_pending'] = $this->getTaskByStatusInMyTask(2);
        $myTask['task_ready_for_test'] = $this->getTaskByStatusInMyTask(3);
        $pending = $em->getRepository('AppBundle:State')->findOneById(2);
        $myTask['color_pending'] = $pending->getColor();
        $myTask['color_ready_for_test'] = $ready_for_test->getColor();
//----------END TAB MY TASK--------------------------------------------------------------

        return $this->render('AppBundle:Admin:dashboard.html.twig', array(
            'information' => $information,
            'manage' => $manage,
            'myTask' => $myTask
        ));
    }

    public function getAjaxTaskPendingInTabMyTaskAction(Request $request){
        $tasks = $this->getTaskByStatusInMyTask(2);
        return new \Symfony\Component\HttpFoundation\JsonResponse($tasks);
    }

    public function getAjaxTaskReadyForTestInTabManagerAction(Request $request){
        $tasks = $this->getTaskReadyForTestOfTabManage();
        return new \Symfony\Component\HttpFoundation\JsonResponse($tasks);
    }

    public function getAjaxAllTaskAction(Request $request){
       $em = $this->getDoctrine()->getManager();

        $userId = $this->getUser()->getId();
        if($this->getUser()->getRol() == 'ROLE_ADMIN')
            $userId = null;

       $all_task_amount = $em->getRepository('AppBundle:Task')->getResumeCountTaskByStatus(null, $userId, 1);
       return new \Symfony\Component\HttpFoundation\JsonResponse($all_task_amount);
    }

    public function getAjaxMyTaskAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $my_task_amount = $em->getRepository('AppBundle:Task')->getResumeCountMyTaskByStatus(null, $this->getUser()->getId(), 1);
        return new \Symfony\Component\HttpFoundation\JsonResponse($my_task_amount);
    }

    public function getAjaxTaskByStatusInMyTaskAction(Request $request){
        $id_status = $request->get('id_status', 0);
        $myTask = $this->getTaskByStatusInMyTask($id_status);
        return new \Symfony\Component\HttpFoundation\JsonResponse($myTask);
    }

    private function getTaskByStatusInMyTask($status){
        $em = $this->getDoctrine()->getManager();
        $tasks_status = $em->getRepository('AppBundle:Task')->getTaskWithUserAssignedTask(null, array($status), $this->getUser()->getId(), 1);
        return $tasks_status;
    }

    private function getTaskReadyForTestOfTabManage(){
        $em = $this->getDoctrine()->getManager();
        $tasks_ready_for_test = $em->getRepository('AppBundle:Task')->getTaskWithUserCreatedTask(null, array(3), $this->getUser()->getId(), 1);
        return $tasks_ready_for_test;
    }

    private function getProyectsOfTabManage(){
        $em = $this->getDoctrine()->getManager();
        $all_proyects = $this->getUser()->getProyect();
        $result_proyect = array();

        foreach($all_proyects as $proyect){
            if($proyect->getEnabled()){
                $temp = array();
                $temp['name'] = $proyect->getName();
                $temp['id'] = $proyect->getId();
                $temp['amount_task'] = $em->getRepository('AppBundle:Task')->getCountTaskByProyectAndStatusAndUserCreatedtask($proyect->getId(),null, $this->getUser()->getId(), 1);
                $temp['complete_task'] = $em->getRepository('AppBundle:Task')->getCountTaskByProyectAndStatusAndUserCreatedtask($proyect->getId(),4, $this->getUser()->getId(), 1);
                $temp['percent'] = $this->calculatePercent($temp['complete_task'], $temp['amount_task']);
                $result_proyect[] = $temp;
            }
        }
        return $result_proyect;
    }

    private function calculatePercent($value, $total){
        if($value == 0 && $total == 0)
            return 100;
        else if($value == 0)
            return 0;
        else{
            return round($value / $total * 100, 0);
        }
    }

    private function getProyectByStatus(){
        $proyects =  $this->getUser()->getProyect();
        $amount_active_proyect = 0;
        $amount_inactive_proyect = 0;

        foreach($proyects as $item){
            if($item->getEnabled())
                $amount_active_proyect ++;
            else
                $amount_inactive_proyect++;
        }
        $result = array();
        $result['active'] = $amount_active_proyect;
        $result['inactive'] = $amount_inactive_proyect;

        return $result;
    }

    //Includes
    public function leftMenuAction(Request $request) {
        $session = $request->getSession();
        //get diff time
        $now = new \DateTime('now');
        $leftMenuUpdated = $session->get('left_menu_updated', new \DateTime('-5 min'));
        $diff = $now->diff($leftMenuUpdated);
        if ($diff->i >= 5) {
            $em = $this->getDoctrine()->getManager();
            $counts = array();
            $counts['users'] = $em->getRepository('AppBundle:User')->getCount();
            $counts['countries'] = $em->getRepository('AppBundle:Country')->getCount();
            $session->set('counts', $counts);
            return new \Symfony\Component\HttpFoundation\JsonResponse(false);
        }
        else
            return new \Symfony\Component\HttpFoundation\JsonResponse(false);
    }

}
