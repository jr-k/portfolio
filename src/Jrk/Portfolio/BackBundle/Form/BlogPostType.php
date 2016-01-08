<?php

namespace Jrk\Portfolio\BackBundle\Form;

use Jrk\Portfolio\CoreBundle\Enum\EnumGlobalYesNo;
use Jrk\Portfolio\CoreBundle\Enum\EnumGlobalYesNoType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BlogPostType extends AbstractType
{


    private $localeManager;

    public function __construct($localeManager)
    {
        $this->localeManager = $localeManager;
    }




    public function buildForm(FormBuilderInterface $builder, array $options)
    {



        $builder->add('translations', 'a2lix_translations', array(
            'locales' => $this->localeManager->getLocales(),
            'fields' => array(
                'subject' => array('label' => 'Titre :','field_type' => 'text','required' => false),
                'excerpt' => array('label' => 'Résumé :','field_type' => 'ckeditor', 'config_name' => 'basic_config','required' => false),
                'content' => array('label' => 'Article :','field_type' => 'ckeditor', 'config_name' => 'basic_config','required' => false),
                'slug' => array('display' => false, 'required' => false),
            ),
            'label' => ' '
        ));

        $builder
            ->add('attachment', 'file', array(
                'label' => 'Photo (360x200) : ',
                'required' => false
            ))


            ->add('published','datetime',array(
                'label' => 'Date de publication :',
            ))

            ->add('active','choice',array(
                'label' => 'Active :',
                'multiple' => false,
                'expanded' => false,
                'choices' => EnumGlobalYesNo::$TOSTRING
            ))

            ->add('created','date',array(
                'label' => 'Date d\'ajout :',
                'disabled' => true
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
            'data_class' => 'Jrk\Portfolio\CoreBundle\Entity\BlogPost'
        ));
    }

    public function getName()
    {
        return 'jrk_portfolio_blog_post';
    }
}
