<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\AdminTreeBundle\Controller;

use Doctrine\ORM\EntityRepository;
use FSi\Bundle\AdminBundle\Admin\CRUD\DataIndexerElement;
use FSi\Bundle\AdminBundle\Doctrine\Admin\Element;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

class ReorderController
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function moveUpAction(DataIndexerElement $element, $id, Request $request)
    {
        $this->getRepository($element)->moveUp($this->getEntity($element, $id));
        $this->flush($element);

        return $this->getRedirectResponse($element, $request);
    }

    public function moveDownAction(DataIndexerElement $element, $id, Request $request)
    {
        $this->getRepository($element)->moveDown($this->getEntity($element, $id));
        $this->flush($element);

        return $this->getRedirectResponse($element, $request);
    }

    /**
     * @param DataIndexerElement $element
     * @param mixed $id
     * @throws NotFoundHttpException
     * @return Object
     */
    private function getEntity(DataIndexerElement $element, $id)
    {
        $entity = $element->getDataIndexer()->getData($id);

        if (null === $entity) {
            throw new NotFoundHttpException(sprintf(
                'Entity for element "%s" with id "%s" was not found!',
                $element->getId(),
                $id
            ));
        }

        return $entity;
    }

    private function getRepository(Element $element): NestedTreeRepository
    {
        $repository = $element->getRepository();
        $this->assertCorrectRepositoryType($repository);

        return $repository;
    }

    private function assertCorrectRepositoryType(EntityRepository $repository): void
    {
        if (false === $repository instanceof NestedTreeRepository) {
            throw new InvalidArgumentException(
                sprintf("Entity must have repository class 'NestedTreeRepository'")
            );
        }
    }

    private function flush(Element $element): void
    {
        $element->getObjectManager()->flush();
    }

    private function getRedirectResponse(DataIndexerElement $element, Request $request): RedirectResponse
    {
        if ($request->query->get('redirect_uri')) {
            $uri = $request->query->get('redirect_uri');
        } else {
            $uri = $this->router->generate(
                $element->getRoute(),
                $element->getRouteParameters()
            );
        }

        return new RedirectResponse($uri);
    }
}
