<?php

namespace Jrk\Portfolio\BackBundle\Form;

use Jrk\Portfolio\CoreBundle\Enum\EnumConfigurationVartype;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConfigurationType extends AbstractType
{

    private $vartype;
    private $hastitle;
    private $localeManager;

    public function __construct($vartype, $hastitle, $localeManager)
    {
        $this->vartype = $vartype;
        $this->hastitle = $hastitle;
        $this->localeManager = $localeManager;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $fields = array(
            'slug' => array(
                'display' => false,
                'required' => false
            ),
            'title' => array(
                'display' => false,
                'required' => false
            ),
            'value' => array(
                'display' => false,
                'required' => false
            )
        );

        if ($this->vartype == EnumConfigurationVartype::FILEUPLOAD) {
            $fields['attachment'] = array(
                'label' => 'Fichier :',
                'field_type' => 'file',
                'required' => false
            );
        } else {

            // No label for value field
            $valueOptions = array('label' => ' ');

            // File association
            $valueOptions['field_type'] = EnumConfigurationVartype::$FILE_ASSOCIATION[$this->vartype];

            // Richtext need a configuration
            if ($this->vartype == EnumConfigurationVartype::RICHTEXT) {
                $valueOptions['config_name'] = 'basic_config';
            }

            // Update options of the "VALUE" field
            $fields['value'] = $valueOptions;
        }

        if ($this->hastitle) {
            $fields['title'] = array(
                'label'=>'Titre',
                'field_type' => 'text'
            );
        }

        $builder
            ->add('translations', 'a2lix_translations', array(
                'locales' => $this->localeManager->getLocales(),
                'fields' => $fields
            ))
        ;


        $builder
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
            'translation_domain' => 'JrkPortfolioCoreBundle',
            'data_class' => 'Jrk\Portfolio\CoreBundle\Entity\Configuration'
        ));
    }

    public function getName()
    {
        return 'jrk_portfolio_configuration';
    }
}
