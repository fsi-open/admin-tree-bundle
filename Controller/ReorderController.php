<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

    /**
     * @param DataIndexerElement $element
     * @param mixed $id
     * @param Request $request
     * @return RedirectResponse
     */
    public function moveUpAction(DataIndexerElement $element, $id, Request $request)
    {
        $this->getRepository($element)->moveUp($this->getEntity($element, $id));

        $this->flush($element);

        return $this->getRedirectResponse($element, $request);
    }

    /**
     * @param DataIndexerElement $element
     * @param mixed $id
     * @param Request $request
     * @return RedirectResponse
     */
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

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        return $entity;
    }

    /**
     * @param Element $element
     * @return NestedTreeRepository
     */
    private function getRepository(Element $element)
    {
        $repository = $element->getRepository();
        $this->assertCorrectRepositoryType($repository);

        return $repository;
    }

    /**
     * @param EntityRepository $repository
     * @throws InvalidArgumentException
     */
    private function assertCorrectRepositoryType($repository)
    {
        if (!$repository instanceof NestedTreeRepository) {
            throw new InvalidArgumentException(
                sprintf("Entity must have repository class 'NestedTreeRepository'")
            );
        }
    }

    /**
     * @param Element $element
     */
    private function flush(Element $element)
    {
        $element->getObjectManager()->flush();
    }

    /**
     * @param DataIndexerElement $element
     * @param Request $request
     * @return RedirectResponse
     */
    private function getRedirectResponse(DataIndexerElement $element, Request $request)
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
