<?php

namespace Jrk\Portfolio\BackBundle\Form;

use Jrk\Portfolio\CoreBundle\Enum\EnumConfigurationVartype;
use Jrk\Portfolio\CoreBundle\Enum\EnumGlobalYesNo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConfigurationAddType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name','text',array(
                'label' => 'Clée :'
            ))
            ->add('description','text',array(
                'label' => 'Description :',
                'required' => false
            ))

            ->add('help','ckeditor', array(
                'label' => 'Aide :',
                'config_name' => 'basic_config'
            ))
            ->add('visible', 'choice', array(
                'label' => 'Visible :',
                'choices' => EnumGlobalYesNo::$TOSTRING,
                'multiple' => false,
                'expanded' => false
            ))
            ->add('hastitle', 'choice', array(
                'label' => 'Possède un titre :',
                'choices' => EnumGlobalYesNo::$TOSTRING,
                'multiple' => false,
                'expanded' => false
            ))
            ->add('vartype', 'choice', array(
                'label' => 'Type de champ :',
                'choices' => EnumConfigurationVartype::$TOSTRING,
                'multiple' => false,
                'expanded' => false
            ))


            ->add('save','submit', array(
                'label' => 'Appliquer et quitter'
            ))
            ->add('save_and_stay','submit', array(
                'label' => 'Enregistrer'
            ))
            ->add('save_and_add','submit', array(
                'label' => 'Appliquer et ajouter'
            ))
            ->add('save_and_edit','submit', array(
                'label' => 'Appliquer et modifier'
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'JrkPortfolioCoreBundle',
            'data_class' => 'Jrk\Portfolio\CoreBundle\Entity\Configuration'
        ));
    }

    public function getName()
    {
        return 'jrk_basic_configuration';
    }
}
