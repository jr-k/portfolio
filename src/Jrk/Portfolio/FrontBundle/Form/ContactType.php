<?php

namespace Jrk\Portfolio\FrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('firstname','text',array(
                'label' => 'Firstname'
            ))

            ->add('lastname','text',array(
                'label' => 'Lastname'
            ))

            ->add('email', 'email', array(
                'label' => 'Email'
            ))

            ->add('message', 'textarea', array(
                'label' => 'Message'
            ))

            ->add('save', 'submit', array(
                'label' => 'Submit'
            ))
        ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => array('contact'),
            'cascade_validation' => true,
            'data_class' => 'Jrk\Portfolio\CoreBundle\Entity\Contact'
        ));
    }

    public function getName()
    {
        return 'contact';
    }

}
