<?php

namespace Jrk\Portfolio\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * BlogPost
 *
 * @ORM\Table(name="portfolio_blog_post")
 * @ORM\Entity(repositoryClass="Jrk\Portfolio\CoreBundle\Repository\BlogPostRepository")
 */
class BlogPost
{

    use \A2lix\I18nDoctrineBundle\Doctrine\ORM\Util\Translatable;

    protected $translations;



    public $attachment;

    public $uploadDirectory = 'uploads/blog/';

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
    private $id;


    /**
     * @var string
     *
     * @ORM\Column(name="published", type="datetime")
     */
    private $published;


    /**
     * @var string
     *
     * @ORM\Column(type="boolean")
     */
    protected $active;


    /**
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     *
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="post")
     */
    private $comments;



    public function __construct()
    {
        $this->published = new \DateTime();
        $this->active = false;
    }

    public function getFile($locale = 'fr')
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

        $dir = $this->uploadDirectory.'b_'.$this->getId().$locale;

        if (!is_dir($dir) && $create) {
            mkdir($dir,0777,true);
        }

        return $dir;
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
     * Set created
     *
     * @param \DateTime $created
     * @return BlogPost
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set published
     *
     * @param \DateTime $published
     * @return BlogPost
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return \DateTime 
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return BlogPost
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
     * Add comments
     *
     * @param \Jrk\Portfolio\CoreBundle\Entity\Comment $comments
     * @return BlogPost
     */
    public function addComment(\Jrk\Portfolio\CoreBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Jrk\Portfolio\CoreBundle\Entity\Comment $comments
     */
    public function removeComment(\Jrk\Portfolio\CoreBundle\Entity\Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }
}
