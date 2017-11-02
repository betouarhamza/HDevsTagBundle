<?php
/**
 * Created by Hamza Betouar
 * Email: betouar.hamza.89@gmail.com
 */
namespace HDevs\TagBundle\Behavior;

use HDevs\TagBundle\Model\Tag;

interface Taggable{

    public function getTagsText();
    public function getTags();
    public function addTag(Tag $tag);
    public function removeTag(Tag $tag);

}