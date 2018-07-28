<?php

namespace AppBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use AppBundle\Util\Helpers;
use AppBundle\Entity\Task;

class TasksDailyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('it:tasks:daily')
            ->setDefinition(array())
            ->setDescription('Run all daily tasks');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $this->getContainer()->get('doctrine')->getManager();

        $output->writeln('run daily task ...');
        $this->createTaskByFrequency($em,$container);
        $output->writeln('Daily Task Finished');

    }

    private function createTaskByFrequency(EntityManager $em, $container){
        $task_with_frequency = $em->getRepository('AppBundle:Task')->getTaskWithFrequencyActive();
        $today = new \DateTime('now');
        
        foreach($task_with_frequency as $objTask){
            $time = $objTask->getFrequency()->getTime();
            if($time != null && $time != ''){
                $date = ($objTask->getFrequencyDate() != null && $objTask->getFrequencyDate() != '')?$objTask->getFrequencyDate(): $today;

                $frequency_date = Helpers::getDateByTime($time, $date);

                if(strtotime($today->format('d-m-Y')) >= strtotime($frequency_date->format('d-m-Y'))){
                    $objTask->setFrequencyDate($today);
                    
                    $newtask = new Task();
                    $newtask->setFrequencyEnable(0);
                    $newtask->setName($objTask->getName());
                    $newtask->setUser($objTask->getUser());
                    $newtask->setDescription($objTask->getDescription());
                    
                    if($objTask->getStartTime()!=null && $objTask->getStartTime()!='' && $objTask->getEndTime()!=null && $objTask->getEndTime()!=''){
                        $diff = $objTask->getStartTime()->diff($objTask->getEndTime());
                        $new_end_date = Helpers::getDateByTime($diff->format('%R%a day'), $today);
                        $newtask->setStartTime($today);
                        $newtask->setEndTime($new_end_date);
                    }else{
                        $newtask->setStartTime($objTask->getStartTime());
                        $newtask->setEndTime($objTask->getEndTime());
                    }
                    $newtask->setPriority($objTask->getPriority());
                    $newtask->setProyect($objTask->getObjProyect());
                    $newtask->setUserCreatedTask($objTask->getUserCreatedTask());
                    $newtask->setInstructions($objTask->getInstructions());
                    
                    $status = 2;
                    if(!$newtask->getUser()){
                        $status = 1;
                        $newtask->setStartTime(null);
                    }
                    $newtask->setState($em->getRepository('AppBundle:State')->findOneById($status));
                    
                    if($status == 2){
                        $message = Helpers::messageNewTask($container, $newtask, $objTask->getObjProyect()->getName());
                        $container->get('mailer')->send($message);
                    }
                    
                    $em->persist($objTask);
                    $em->persist($newtask);
                    $em->flush();
                }
            }
        }
    }

}