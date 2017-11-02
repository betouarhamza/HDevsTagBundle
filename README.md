Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require hdevs/tag-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new HDevs\TagBundle\HDevsTagBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3: Example
-------------------------
in app/config/config.yml
```yml
h_devs_tag:
    entity_class: AppBundle\Entity\Tag
```

Tag Entity
```php
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use HDevs\TagBundle\Model\Tag as BaseTag

/**
 * Tag
 *
 * @ORM\Table(name="tag")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TagRepository")
 */
class Tag extends BaseTag
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
``` 

Post Entity
```php
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use HDevs\TagBundle\Behavior\Taggable;

/**
 * Event
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRepository")
 */
class Post implements Taggable
{
    // other fields

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tag")
     */
    private $tags;

    /**
     * @var string
     */
    private $tagsText;


    public function __construct(){
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add tag
     *
     * @param \HDevs\TagBundle\Model\Tag $tag
     *
     * @return Event
     */
    public function addTag(\HDevs\TagBundle\Model\Tag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tag
     *
     * @param \HDevs\TagBundle\Model\Tag $tag
     */
    public function removeTag(\HDevs\TagBundle\Model\Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return string
     */
    public function getTagsText()
    {
        return $this->tagsText;
    }

    /**
     * @param string $tagsText
     */
    public function setTagsText($tagsText)
    {
        $this->tagsText = $tagsText;
    }


}
``` 

Step 4: Add behavior
-------------------------
src/AppBundle/Behavior/TagBehavior
```php
namespace AppBundle\Behavior;


use HDevs\TagBundle\Model\Tag;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use HDevs\TagBundle\Behavior\TagBehavior as TagBehaviorInterface

class TagBehavior implements TagBehaviorInterface
{

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function run(Tag $tag)
    {
        $tag->setUser($this->tokenStorage->getToken()->getUser());
    }

    public function validate(Tag $tag)
    {
        if( $this->tokenStorage->getToken()->getUser() != $tag->getUser() ){
            $t = new \AppBundle\Entity\Tag();
            $t->setUser($this->tokenStorage->getToken()->getUser());
            $t->setValue($tag->getValue());
            return $t;
        }
        return $tag;
    }
}
```

in app/config/services.yml
```yml
services:
    #other services
    
    app.behavior.tag:
        class: AppBundle\Behavior\TagBehavior
        arguments: ["@security.token_storage"]
        public: true
    
```

in app/config/config.yml
```yml
h_devs_tag:
    entity_class: AppBundle\Entity\Tag
    behavior_class: app.behavior.tag
```