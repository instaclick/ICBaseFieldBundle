<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Core\FieldBundle\Tests\MockObject;

use IC\Bundle\Core\FieldBundle\Entity\FieldContentAware;

/**
 * Mock Dynamic Entity
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class MockDynamicEntity extends FieldContentAware
{
    /**
     * @var mixed
     */
    private $foo;

    /**
     * @var mixed
     */
    private $bar;

    /**
     * Retrieve foo
     *
     * @return mixed
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * Define foo
     *
     * @param mixed $foo foo
     */
    public function setFoo($foo)
    {
        $this->foo = $foo;
    }

    /**
     * Retrieve bar
     *
     * @return mixed
     */
    public function getBar()
    {
        return $this->bar;
    }

    /**
     * Define bar
     *
     * @param mixed $bar bar
     */
    public function setBar($bar)
    {
        $this->bar = $bar;
    }
}
