<?php

namespace AppBundle\Util;
require_once(dirname(__FILE__).'/../lib/Mobile-Detect/Mobile_Detect.php');

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\HttpFoundation\Response;

class Helpers
{
    public static function getSlug($string, $separative = '-')
    {
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        $slug = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $slug);
        $slug = strtolower(trim($slug, $separative));
        $slug = preg_replace("/[\/_|+ -]+/", $separative, $slug);

        return $slug;
    }

    public static function genDay()
    {
        $days = array();
        $days[''] = 'any';
        for ($index = 1; $index < 32; $index++) {
            $days[$index] = $index;
        }
        return $days;
    }

    public static function getMonth(){
        $month = array(
            '' => 'any',
            '1' => 'January',
            '2' => 'February',
            '3' => 'March',
            '4' => 'April',
            '5' => 'May',
            '6' => 'June',
            '7' => 'July',
            '8' => 'August',
            '9' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        );
        return $month;
    }

    public static function getYears(){
        $date = new \DateTime('now');
        $year = $date->format('Y');
        $start_year = 1850;
        $range =  ((int)$year - $start_year) + 1;
        $years = array();
        $years[''] = 'none';
        for ($index = 0; $index < $range; $index++) {
            $years[$start_year] = $start_year;
            $start_year++;
        }
        return $years;
    }

    public static function existNull($list, $id)
    {
        $result = false;
        foreach($list as $elem){
            if($elem == $id)
                $result = true;
        }
        return $result;
    }

    public static function existImageCase($caseId , $path){
        $result = false;
        if(file_exists($path.$caseId.".JPG"))
            $result = true;
        elseif(file_exists($path.$caseId.".jpg"))
            $result = true;
        return $result;
    }

    public static function notExistImageCase($caseId , $path){
        $result = true;
        if(file_exists($path.$caseId.".JPG"))
            $result = false;
        elseif(file_exists($path.$caseId.".jpg"))
            $result = false;
        return $result;
    }

    public static function getEntityName($entity){
        $dir = explode('\\', $entity);
        $name = '';
        if(count($dir) > 0)
            $name = $dir[count($dir)-1];
        return $name;
    }

    public static function getPaginate($start, $length , $cases){
        $count = count($cases);
        $temp = array();
        $end = ($start + $length);
        if($start < $count){
            for ($i = $start; $i < $end; $i++) {
                if($i == $count)
                    break;
                else{
                    $temp[] = $cases[$i];
                }
            }
        }
        return $temp;

    }

    public static function getObjectDateTime($date, $delimiter)
    {
        $array_date = explode($delimiter, $date);
        $datetime = new \DateTime();
        $datetime->setDate($array_date[2], $array_date[0], $array_date[1]);
        return $datetime;
    }
	
    public static function getDatetimeByString($date, $format, $delimiter){
        $list_dates = explode($delimiter, $date);
        $list_format = explode($delimiter, $format);
        
        $obj_dates = array();
        for($i=0; $i<count($list_format); $i++)
            $obj_dates[$list_format[$i]] = $list_dates[$i];
        return $obj_dates;
    }

    public static function getArrayProyect($proyects){
        $proyect_select = array();
        foreach($proyects as $obj)
            $proyect_select[$obj->getId()] = $obj->getName();
        return $proyect_select;
    }

    public static function messageNewTask($container, $task, $project, $subject = null){
        $action = 'New';
        $message_subject = 'You have a new task to complete';
        if($subject != null){
            $message_subject = $subject;
            $action = 'Edit';
        }

        $bodyHTML = $container->get('twig')->render('AppBundle:Admin/emails:new_task.html.twig', array(
           'task' => $task,
           'proyect' => $project,
           'action' => $action
        ));

        $bodyText = $container->get('twig')->render('AppBundle:Admin/emails:new_task.txt.twig', array(
            'task' => $task,
            'proyect' => $project,
            'action' => $action
        ));


        return Helpers::buildMessageEmail($message_subject,  $task->getUser()->getEmail(), $bodyHTML, $bodyText);
    }

    public static function messageResolvedTask($container, $task, $project){
        $bodyHTML = $container->get('twig')->render('AppBundle:Admin/emails:resolved_task.html.twig', array(
            'task' => $task,
            'project' => $project->getName()
        ));

        $bodyText = $container->get('twig')->render('AppBundle:Admin/emails:resolved_task.txt.twig', array(
            'task' => $task,
            'project' => $project->getName()
        ));

        return Helpers::buildMessageEmail('Task resolved',  $task->getUserCreatedTask()->getEmail(), $bodyHTML, $bodyText);
    }

    public static function messageWelcomeUser($container, $user, $password){
        $bodyHTML = $container->get('twig')->render('AppBundle:Admin/emails:welcome_user.html.twig', array(
            'user' => $user,
            'password' => $password
        ));

        $bodyText = $container->get('twig')->render('AppBundle:Admin/emails:welcome_user.txt.twig', array(
            'user' => $user,
            'password' => $password
        ));

        return Helpers::buildMessageEmail('Welcome to Taskit',  $user->getEmail(), $bodyHTML, $bodyText);
    }

    public static function messageDesapproveTask($container, $task, $project){
        $bodyHTML = $container->get('twig')->render('AppBundle:Admin/emails:desapprove_task.html.twig', array(
            'task' => $task,
            'project' => $project
        ));

        $bodyText = $container->get('twig')->render('AppBundle:Admin/emails:desapprove_task.txt.twig', array(
            'task' => $task,
            'project' => $project
        ));

        return Helpers::buildMessageEmail('Task incomplete',  $task->getUser()->getEmail(), $bodyHTML, $bodyText);
    }

    public static function messageCompletetask($container, $task, $project){
        $bodyHTML = $container->get('twig')->render('AppBundle:Admin/emails:complete_task.html.twig', array(
            'task' => $task,
            'project' => $project
        ));

        $bodyText = $container->get('twig')->render('AppBundle:Admin/emails:complete_task.txt.twig', array(
            'task' => $task,
            'project' => $project
        ));

        return Helpers::buildMessageEmail('Task completed',  $task->getUserCreatedTask()->getEmail(), $bodyHTML, $bodyText);
    }

    public static function messageRemoveTask($container, $user, $task){
        $bodyHTML = $container->get('twig')->render('AppBundle:Admin/emails:remove_task.html.twig', array(
            'name' => $user->getName(),
            'task' => $task
        ));

        $bodyText = $container->get('twig')->render('AppBundle:Admin/emails:remove_task.txt.twig', array(
            'name' => $user->getName(),
            'task' => $task
        ));
        return Helpers::buildMessageEmail('Removed task',  $user->getEmail(), $bodyHTML, $bodyText);
    }

    public static function buildMessageEmail($subject, $email, $bodyHTML, $bodyText){
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(array('info@comvxo.com' => 'taskit'))
            ->setTo($email)
            ->setBody($bodyHTML, 'text/html')
            ->addPart($bodyText, 'text/plain');
        return $message;
    }
    
    public static function getProgressProject($resumenTask){
        $information = array();
        $complete = 0;
        $total = 0;
        $percent = 0;
        foreach ($resumenTask as $values) {
            $total+=$values['amount'];
            if($values['name'] == 'Completed')
                $complete = $values['amount'];
        }
        if($complete != 0 || $total != 0)
            $percent = round($complete / $total * 100, 0);
        $information = ['completed'=> $complete, 'total'=> $total, 'percent'=> $percent];
        return $information;
    }

    /**
     * Return a new response 404setAuditstype
     *
     * @param  Controller $controller
     * @param  string $text
     * @param  string $btnText
     * @param  string $btnIcon
     * @param  string $btnUrl
     * @param  boolean $newWindow
     * @return response
     */
    public static function error404($controller, $text, $btnText, $btnIcon, $btnUrl)
    {
        return $controller->render('AppBundle:Admin/errors:404.html.twig', array(
            'text' => $text,
            'btnText' => $btnText,
            'btnIcon' => $btnIcon,
            'btnUrl' => $btnUrl
        ), new Response('', 404, array(
                'Content-Type' => 'text/html'
            ))
        );
    }
}
