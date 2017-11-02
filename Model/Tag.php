<?php
/**
 * Created by Hamza Betouar
 * Email: betouar.hamza.89@gmail.com
 */

namespace HDevs\TagBundle\Model;


use Doctrine\ORM\Mapping as ORM;

class Tag
{

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=120)
     */
    protected $value;

    /**
     * Set value
     *
     * @param string $value
     *
     * @return Tag
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

}