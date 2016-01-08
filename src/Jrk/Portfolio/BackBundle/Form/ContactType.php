<?php

namespace Jrk\Portfolio\BackBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('firstname','text',array(
                'label' => 'Prénom :'
            ))
            ->add('lastname','text',array(
                'label' => 'Nom :'
            ))
            ->add('email','email',array(
                'label' => 'Email :'
            ))
            ->add('subject','text',array(
                'label' => 'Sujet :'
            ))
            ->add('message','textarea',array(
                'label' => 'Message :'
            ))

            ->add('created','date',array(
                'label' => 'Date d\'ajout :',
                'disabled' => true
            ))

            ->add('save','submit', array(
                'label' => 'Appliquer et quitter'
            ))
            ->add('save_and_stay','submit', array(
                'label' => 'Enregistrer'
            ))
          
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jrk\Portfolio\CoreBundle\Entity\Contact'
        ));
    }

    public function getName()
    {
        return 'jrk_portfolio_contact';
    }
}
