<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */

    private $proyects;
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $priority = array(
            'Small' => 'Small',
            'Medium' => 'Medium',
            'High' => 'High',
        );
        $builder
            ->add('name', 'text', array(
                'max_length' => 70
            ))
            ->add('description', 'textarea', array(
                'required' => false,
                'max_length' => 250
            ))
            ->add('end_time', 'text')
            ->add('user')
            ->add('proyect')
            ->add('proyect', 'choice', array(
                'choices' => $this->proyects,
            ))
            ->add('priority', 'choice', array(
                'choices' => $priority,
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Task',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_task';
    }

    public function setProyects($proyects) {
        $this->proyects = $proyects;
    }
}
