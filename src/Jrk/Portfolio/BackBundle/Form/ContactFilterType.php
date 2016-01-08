<?php

namespace Jrk\Portfolio\BackBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Lexik\Bundle\FormFilterBundle\Filter\FilterOperands;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;

class ContactFilterType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('sortByWay', 'filter_text',array(
                'label' => false,
                'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
                        if (isset($values['value'])){
                            $orderBW = explode(';',$values['value']);
                            $filterQuery->getQueryBuilder()->orderBy($orderBW[0],$orderBW[1]);
                        }
                        return null;
                    },
                'attr' => array('class' => 'sortByWay hidden')
            ))




            ->add('firstname', 'filter_text', array(
                'condition_pattern' => FilterOperands::STRING_BOTH,
                'label' => 'Prénom :'
            ))
            ->add('lastname', 'filter_text', array(
                'condition_pattern' => FilterOperands::STRING_BOTH,
                'label' => 'Nom :'
            ))

            ->add('email', 'filter_text', array(
                'condition_pattern' => FilterOperands::STRING_BOTH,
                'label' => 'Email :'
            ))

            ->add('subject', 'filter_text', array(
                'condition_pattern' => FilterOperands::STRING_BOTH,
                'label' => 'Sujet :'
            ))

            ->add('created', 'filter_date_range', array(
                'left_date_options' => array('label' => 'Envoyé entre le :','format' => 'dd/MM/y', 'widget' => 'single_text'),
                'right_date_options' =>  array('label' => 'et le :','format' => 'dd/MM/y', 'widget' => 'single_text'),
            ))

        ;

    }

    public function getName()
    {
        return 'jrk_portfolio_contactfiltertype';
    }
}
