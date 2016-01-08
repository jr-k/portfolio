<?php

namespace Jrk\Portfolio\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Country
 *
 * @ORM\Table(name="portfolio_country")
 * @ORM\Entity(repositoryClass="Jrk\Portfolio\CoreBundle\Repository\CountryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Country
{

    public $uploadDirectory = 'uploads/country/';
    public $resourceDirectory = 'resources/country/';

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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    
    /**
     * @var string
     *
     * @ORM\Column(name="country_default", type="boolean", nullable=true)
     */
    private $default;

    /**
     * @ORM\OneToMany(targetEntity="Jrk\Portfolio\CoreBundle\Entity\Locale",mappedBy="country")
     */
    private $locales;

    
    public function __toString()
    {
        return $this->getName();
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
     * Set position
     *
     * @param integer $position
     * @return Country
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
     * Set name
     *
     * @param string $name
     * @return Country
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Country
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
     * Set default
     *
     * @param boolean $default
     * @return Country
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
     * Add locales
     *
     * @param \Jrk\Portfolio\CoreBundle\Entity\Locale $locales
     * @return Country
     */
    public function addLocale(\Jrk\Portfolio\CoreBundle\Entity\Locale $locales)
    {
        $this->locales[] = $locales;

        return $this;
    }

    /**
     * Remove locales
     *
     * @param \Jrk\Portfolio\CoreBundle\Entity\Locale $locales
     */
    public function removeLocale(\Jrk\Portfolio\CoreBundle\Entity\Locale $locales)
    {
        $this->locales->removeElement($locales);
    }

    /**
     * Get locales
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLocales()
    {
        return $this->locales;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Country
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }
}
