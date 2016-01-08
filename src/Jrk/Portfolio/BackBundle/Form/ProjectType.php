<?php

namespace Jrk\Portfolio\BackBundle\Form;

use Jrk\Portfolio\CoreBundle\Enum\EnumGlobalYesNo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectType extends AbstractType
{



    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('attachment', 'file', array(
                'label' => 'Photo (528x284) : ',
                'required' => false
            ))


            ->add('title','text',array(
                'required' => true,
                'label' => 'Nom'
            ))



            ->add('url','text',array(
                'required' => true,
                'label' => 'URL'
            ))


            ->add('category','text',array(
                'required' => true,
                'label' => 'Catégorie'
            ))

            ->add('skills','text',array(
                'required' => false,
                'label' => 'Compétences'
            ))

            ->add('description','textarea',array(
                'required' => false,
                'label' => 'Description'
            ))

            ->add('datePublished','date',array(
                'label' => 'Date de publication :',
            ))

            ->add('active','choice',array(
                'label' => 'Active :',
                'multiple' => false,
                'expanded' => false,
                'choices' => EnumGlobalYesNo::$TOSTRING
            ))

            ->add('save','submit', array(
                'label' => 'Appliquer et quitter'
            ))
            ->add('save_and_add','submit', array(
                'label' => 'Appliquer et ajouter'
            ))
            ->add('save_and_stay','submit', array(
                'label' => 'Enregistrer'
            ))
          
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jrk\Portfolio\FrontBundle\Entity\Project'
        ));
    }

    public function getName()
    {
        return 'jrk_portfolio_project';
    }
}
