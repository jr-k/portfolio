<?php

namespace Jrk\Portfolio\BackBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Lexik\Bundle\FormFilterBundle\Filter\FilterOperands;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;

class ProjectFilterType extends AbstractType
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





            ->add('datePublished', 'filter_date_range', array(
                'left_date_options' => array('label' => 'Publié le (Supérieur à) :','format' => 'dd-MMM-yyy', 'years' => range(2014,date('Y')+1)),
                'right_date_options' =>  array('label' => '(Inférieur à) :','format' => 'dd-MMM-yyy', 'years' => range(2014,date('Y')+1)),
            ))


        ;

    }

    public function getName()
    {
        return 'jrk_portfolio_projectfiltertype';
    }
}
