<?php

namespace Jrk\Portfolio\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Jrk\Portfolio\CoreBundle\Enum\EnumConfigurationVartype;

/**
 * Configuration
 *
 * @ORM\Table(name="portfolio_configuration")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Jrk\Portfolio\CoreBundle\Repository\ConfigurationRepository")
 */
class Configuration
{

    use \A2lix\I18nDoctrineBundle\Doctrine\ORM\Util\Translatable;

    protected $translations;


    public $uploadDirectory = 'uploads/configuration/';

    /**
     * @ORM\PreRemove
     */
    public function PreRemove()
    {
        $this->deleteFiles($this->getUploadDirectory(false,''));
    }


    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $help;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $position;

    /**
     * @ORM\Column(type="boolean",nullable=true)
     */
    protected $visible;

    /**
     * @ORM\Column(type="boolean",nullable=true)
     */
    protected $hastitle;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $vartype;


    public function __construct()
    {
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
		$this->visible = true;
		$this->hastitle = false;
		$this->vartype = EnumConfigurationVartype::TEXT;
        $this->position = PHP_INT_MAX;
    }


    public function getId() {
        return $this->id;
    }


    /**
     * Set name
     *
     * @param string $name
     * @return Configuration
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set desc
     *
     * @param string $desc
     * @return Configuration
     */
    public function setDescription($desc) {
        $this->description = $desc;

        return $this;
    }

    /**
     * Get desc
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set vartype
     *
     * @param integer $vartype
     * @return Configuration
     */
    public function setVartype($vartype) {
        $this->vartype = $vartype;

        return $this;
    }

    /**
     * Get vartype
     *
     * @return integer 
     */
    public function getVartype() {
        return $this->vartype;
    }



    /**
     * Set visible
     *
     * @param boolean $visible
     * @return Configuration
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set help
     *
     * @param string $help
     * @return Configuration
     */
    public function setHelp($help)
    {
        $this->help = $help;

        return $this;
    }

    /**
     * Get help
     *
     * @return string 
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Configuration
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set hastitle
     *
     * @param boolean $hastitle
     * @return Configuration
     */
    public function setHastitle($hastitle)
    {
        $this->hastitle = $hastitle;

        return $this;
    }

    /**
     * Get hastitle
     *
     * @return boolean 
     */
    public function getHastitle()
    {
        return $this->hastitle;
    }


    public function getFile($locale = 'en')
    {
        $folder = $this->getUploadDirectory(false, $locale);

        if (!is_dir($folder)) {
            return null;
        }

        $dir = opendir($folder);

        while ($file = readdir($dir)) {
            if ($file == '.' || $file == '..')
                continue;
            if (is_dir($folder . '/' . $file)) {
                continue;
            } else {
                return $folder . '/' . $file;
            }
        }

        return null;
    }


    public function deleteFiles($dir)
    {
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
            }
            rmdir($dir);
        }
    }

    public static function delTree($dir)
    {
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
            }

            return rmdir($dir);
        }

        return null;
    }


    public function getUploadDirectory($create = false, $locale = 'fr')
    {
        $locale = $locale == '' ? '' : '/'.$locale;

        $dir = $this->uploadDirectory.'c_'.$this->getId().$locale;

        if (!is_dir($dir) && $create) {
            mkdir($dir,0777,true);
        }

        return $dir;
    }
}
