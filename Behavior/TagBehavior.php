<?php
/**
 * Created by Hamza Betouar
 * Email: betouar.hamza.89@gmail.com
 */

namespace HDevs\TagBundle\Behavior;


use HDevs\TagBundle\Model\Tag;

interface TagBehavior
{
    public function run(Tag $tag);
    public function validate(Tag $tag);
}