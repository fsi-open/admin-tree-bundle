<?php

namespace spec\FSi\Bundle\AdminTreeBundle;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FSiAdminTreeBundleSpec extends ObjectBehavior
{
    function it_is_bundle()
    {
        $this->shouldHaveType('Symfony\Component\HttpKernel\Bundle\Bundle');
    }
}
