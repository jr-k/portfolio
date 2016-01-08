<?php

namespace Jrk\Portfolio\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Locale
 *
 * @ORM\Table(name="portfolio_locale")
 * @ORM\Entity(repositoryClass="Jrk\Portfolio\CoreBundle\Repository\LocaleRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Locale
{

    public $uploadDirectory = 'uploads/locale/';
    public $resourceDirectory = 'resources/locale/';

    /**
     * @ORM\PreRemove
     */
    public function PreRemove()
    {
        $this->deleteFiles($this->uploadDirectory.'l_'.$this->getId());
    }

    public $attachment;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    
    /**
     * @var string
     *
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    private $position;
    
    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=255)
     */
    private $locale;
    
     /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=255, nullable=true)
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    
    /**
     * @var string
     *
     * @ORM\Column(name="locale_default", type="boolean", nullable=true)
     */
    private $default;

    /**
     * @ORM\ManyToOne(targetEntity="Jrk\Portfolio\CoreBundle\Entity\Country",inversedBy="locales")
     */
    private $country;

    
    public function __toString()
    {
        return $this->getLocale();
    }


    public function __construct()
    {
        $this->position = PHP_INT_MAX;
        $this->default = false;
    }
  

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Locale
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
     * Set locale
     *
     * @param string $locale
     * @return Locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string 
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set language
     *
     * @param string $language
     * @return Locale
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string 
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Locale
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set default
     *
     * @param boolean $default
     * @return Locale
     */
    public function setDefault($default)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * Get default
     *
     * @return boolean 
     */
    public function getDefault()
    {
        return $this->default;
    }


    /**
     * Set active
     *
     * @param boolean $active
     * @return Locale
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }


    /**
     * Get locale
     *
     * @return string
     */
    public function getLocaleCode()
    {
        return $this->getCountry()->getCode().'-'.$this->locale;
    }


    public function getFlag()
    {
        $file = $this->uploadDirectory.'l_'.$this->getId().'/flag.jpg';

        if (file_exists($file)) {
            return $file;
        }

        return $this->resourceDirectory.'flag.jpg';
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


    public function getUploadDirectory($create = false)
    {
        $dir = $this->uploadDirectory.'l_'.$this->getId();

        if (!is_dir($dir) && $create) {
            mkdir($dir,0777,true);
        }

        return $dir;
    }

    /**
     * Set country
     *
     * @param \Jrk\Portfolio\CoreBundle\Entity\Country $country
     * @return Locale
     */
    public function setCountry(\Jrk\Portfolio\CoreBundle\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \Jrk\Portfolio\CoreBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }
}
