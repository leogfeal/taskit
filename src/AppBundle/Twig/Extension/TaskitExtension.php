<?php

namespace AppBundle\Twig\Extension;

use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Util\Helpers;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraints\DateTime;


class TaskitExtension extends \Twig_Extension
{

    private $session;
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function getFilters()
    {
        return array(
        );
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('trigger_error', array($this, 'triggerError')),
            new \Twig_SimpleFunction('status_filter_task', array($this, 'statusFilterTask')),
            new \Twig_SimpleFunction('get_dif_days', array($this, 'getDiffDays')),
        );
    }

    public function getName()
    {
        return 'taskit';
    }

    public function triggerError($string, $error_type = E_USER_DEPRECATED)
    {
        @trigger_error($string, $error_type);
    }

    public function statusFilterTask($status){
		$this->session->set('status_filter_task', $status);
    }

    public function getDiffDays($date){
        $now = new \DateTime('now');
        $diff = $now->diff($date);
        return 'Days: '.$diff->days;
    }
}

?>
