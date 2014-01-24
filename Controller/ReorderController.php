<?php

namespace FSi\Bundle\AdminTreeBundle\Controller;

use Doctrine\ORM\EntityManager;
use FSi\Bundle\AdminBundle\Admin\Doctrine\CRUDElement;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Router;

class ReorderController
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    /**
     * @var \Symfony\Component\Routing\Router
     */
    private $router;

    public function __construct(Router $router, EntityManager $em)
    {
        $this->em = $em;
        $this->router = $router;
    }

    public function moveUpAction(CRUDElement $element, Request $request)
    {
        $entity = $this->getEntity($element, $request->get('id'));

        /** @var \Gedmo\Tree\Entity\Repository\NestedTreeRepository $repository */
        $repository = $element->getRepository();
        $repository->moveUp($entity);

        $this->em->persist($entity);
        $this->em->flush();

        return new RedirectResponse(
            $this->router->generate('fsi_admin_crud_list', array('element' => $element->getId())),
            302
        );
    }

    public function moveDownAction(CRUDElement $element, Request $request)
    {
        $entity = $this->getEntity($element, $request->get('id'));

        /** @var \Gedmo\Tree\Entity\Repository\NestedTreeRepository $repository */
        $repository = $element->getRepository();
        $repository->moveDown($entity);

        $this->em->persist($entity);
        $this->em->flush();

        return new RedirectResponse(
            $this->router->generate('fsi_admin_crud_list', array('element' => $element->getId())),
            302
        );
    }

    /**
     * @param \FSi\Bundle\AdminBundle\Admin\Doctrine\CRUDElement $element
     * @param int $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return Object
     */
    private function getEntity(CRUDElement $element, $id)
    {
        $entity = $element->getDataIndexer()->getData($id);

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        return $entity;
    }
}
