<?php

namespace Jrk\Portfolio\FrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NewsletterType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('lastname','text',array(
                'label' => 'home_section3_lastname'
            ))
            ->add('firstname','text',array(
                'label' => 'home_section3_firstname'
            ))

            ->add('email', 'email', array(
                'label' => 'home_section3_email'
            ))


        ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => array('newsletter'),
            'cascade_validation' => true,
            'data_class' => 'Jrk\Portfolio\CoreBundle\Entity\Newsletter'
        ));
    }

    public function getName()
    {
        return 'newsletter';
    }

}
