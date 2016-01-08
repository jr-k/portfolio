<?php

namespace Jrk\Portfolio\BackBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Lexik\Bundle\FormFilterBundle\Filter\FilterOperands;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;

class ConfigurationFilterType extends AbstractType
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

            ->add('description', 'filter_text', array(
                'condition_pattern' => FilterOperands::STRING_STARTS,
                'label' => 'Nom :'
            ))


        ;

    }

    public function getName()
    {
        return 'jrk_basic_configurationfiltertype';
    }
}
