<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class ProfileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('password', 'repeated', array(
                'type' => 'password',
                'required' => false,
                'constraints' => array(
                    new Length(array(
                        'min' => 6,
                        'minMessage' => 'pwd.min'
                    ))                    
                ),
                'invalid_message' => 'pwd.same',
            ))
            ->add('username', 'text', array(
                'constraints' => array(
                    new NotBlank(array(
                        'message' => 'username.required'
                    ))
                )
            ))
            ->add('email', 'email', array(
                'constraints' => array(
                    new NotBlank(array(
                        'message' => 'email.required'
                    )),
                    new Email(array(
                        'message' => 'email.invalid'
                    ))
                )
            ))
            ->add('phone', 'text')
            ->add('address', 'text')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_user';
    }
}
