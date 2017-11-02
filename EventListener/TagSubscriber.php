<?php
/**
 * Created by Hamza Betouar
 * Email: betouar.hamza.89@gmail.com
 */

namespace HDevs\TagBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use HDevs\TagBundle\Behavior\TagBehavior;
use HDevs\TagBundle\Behavior\Taggable;
use HDevs\TagBundle\Manager\TagManager;
use Psr\Container\ContainerInterface;

class TagSubscriber implements EventSubscriber
{

    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @var string
     */
    private $tagClass;

    /**
     * @var TagBehavior
     */
    private $behavior;

    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'preUpdate',
            'postLoad',
        ];
    }

    public function __construct(Registry $doctrine, TagManager $manager, ContainerInterface $container)
    {
        $this->doctrine = $doctrine;
        $this->tagClass = $manager->getConfig('entity_class');
        if($manager->getConfig('behavior_class'))
            $this->behavior = $container->get($manager->getConfig('behavior_class'));
    }

    public function prePersist(LifecycleEventArgs $event) {
        $this->preEvent($event);
    }
    public function preUpdate(LifecycleEventArgs $event) {
        $this->preEvent($event);
    }
    private function preEvent(LifecycleEventArgs $event) {
        $entity = $event->getEntity();
        if( $entity instanceof Taggable){
            foreach ($entity->getTags() as $t) $entity->removeTag($t);
            $tags = $entity->getTagsText();
            $tags = explode(',', $tags);

            $em = $this->doctrine->getManager();
            foreach($tags as $value){
                $t =$em->getRepository($this->tagClass)
                    ->findOneByValue($value);
                if( $t === null ) {
                    $tag = new $this->tagClass;
                    $tag->setValue($value);
                    if ($this->behavior !== null) {
                        $this->behavior->run($tag);
                    }
                }
                else{
                    $tag = $t;
                    if ($this->behavior !== null) {
                        $tag = $this->behavior->validate($tag);
                    }
                }

                $em->persist($tag);
                $em->flush();
                $entity->addTag($tag);
            }
        }
    }
    public function postLoad(LifecycleEventArgs $event) {

    }


}