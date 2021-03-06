<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\AdminTreeBundle\Event;

use FSi\Bundle\AdminBundle\Admin\Element;
use FSi\Bundle\AdminBundle\Event\AdminEvent;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

final class MovedDownTreeEvent extends AdminEvent
{
    /**
     * @var object
     */
    private $entity;

    /**
     * @param Element $element
     * @param Request $request
     * @param mixed $entity
     * @throws InvalidArgumentException
     */
    public function __construct(Element $element, Request $request, $entity)
    {
        if (false === is_object($entity)) {
            throw new InvalidArgumentException(sprintf(
                'Expected an object, got "%s" instead',
                gettype($entity)
            ));
        }

        parent::__construct($element, $request);
        $this->entity = $entity;
    }

    /**
     * @return object
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
