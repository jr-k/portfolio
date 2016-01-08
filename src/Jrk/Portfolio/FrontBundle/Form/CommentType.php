<?php

namespace Jrk\Portfolio\FrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommentType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('author','text',array(
                'label' => 'comment_author',
                'translation_domain' => 'JrkPortfolioCoreBundle'
            ))
            ->add('message','textarea',array(
                'label' => 'comment_message',
                'translation_domain' => 'JrkPortfolioCoreBundle'
            ))
        ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => array('comment'),
            'cascade_validation' => true,
            'data_class' => 'Jrk\Portfolio\CoreBundle\Entity\Comment'
        ));
    }

    public function getName()
    {
        return 'comment';
    }

}
