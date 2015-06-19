<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\AdminTreeBundle\Controller;

use FSi\Bundle\AdminBundle\Admin\CRUD\DataIndexerElement;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Router;
use FSi\Bundle\AdminBundle\Doctrine\Admin\Element;

class ReorderController
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param DataIndexerElement $element
     * @param $id
     * @param Request $request
     * @return RedirectResponse
     */
    public function moveUpAction(DataIndexerElement $element, $id, Request $request)
    {
        $entity = $this->getEntity($element, $id);

        /** @var $repository \Gedmo\Tree\Entity\Repository\NestedTreeRepository */
        $repository = $element->getRepository();
        $this->assertCorrectRepositoryType($repository);
        $repository->moveUp($entity);

        $this->flush($element, $entity);

        return $this->getRedirectResponse($element, $request);
    }

    /**
     * @param DataIndexerElement $element
     * @param $id
     * @param Request $request
     * @return RedirectResponse
     */
    public function moveDownAction(DataIndexerElement $element, $id, Request $request)
    {
        $entity = $this->getEntity($element, $id);

        /** @var $repository \Gedmo\Tree\Entity\Repository\NestedTreeRepository */
        $repository = $element->getRepository();
        $this->assertCorrectRepositoryType($repository);
        $repository->moveDown($entity);

        $this->flush($element, $entity);

        return $this->getRedirectResponse($element, $request);
    }

    /**
     * @param DataIndexerElement $element
     * @param int $id
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
     * @param $repository
     * @throws \InvalidArgumentException
     * @internal param \FSi\Bundle\AdminBundle\Admin\Doctrine\CRUDElement $element
     */
    private function assertCorrectRepositoryType($repository)
    {
        if (!$repository instanceof NestedTreeRepository) {
            throw new \InvalidArgumentException(
                sprintf("Entity must have repository class 'NestedTreeRepository'")
            );
        }
    }

    /**
     * @param Element $element
     * @param $entity
     */
    private function flush(Element $element, $entity)
    {
        $om = $element->getObjectManager();
        $om->flush();
    }

    /**
     * @param \FSi\Bundle\AdminBundle\Admin\CRUD\DataIndexerElement $element
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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
