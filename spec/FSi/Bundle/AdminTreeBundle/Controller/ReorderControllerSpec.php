<?php

namespace spec\FSi\Bundle\AdminTreeBundle\Controller;

use Doctrine\ORM\EntityManager;
use FSi\Bundle\AdminBundle\Admin\Doctrine\CRUDElement;
use FSi\Component\DataIndexer\DoctrineDataIndexer;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

/**
 * @mixin \FSi\Bundle\AdminTreeBundle\Controller\ReorderController
 */
class ReorderControllerSpec extends ObjectBehavior
{
    function let(
        Router $router,
        EntityManager $em,
        CRUDElement $element,
        DoctrineDataIndexer $indexer
    ) {
        $element->getId()->willReturn('category');
        $element->getDataIndexer()->willReturn($indexer);

        $this->beConstructedWith($router, $em);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('FSi\Bundle\AdminTreeBundle\Controller\ReorderController');
    }

    function it_moves_up_item_when_move_up_action_called(
        CRUDElement $element,
        Request $request,
        NestedTreeRepository $repository,
        \StdClass $category,
        Request $request,
        EntityManager $em,
        Router $router,
        DoctrineDataIndexer $indexer
    ) {
        $request->get('id')->willReturn(1);
        $indexer->getData(1)->willReturn($category);

        $element->getRepository()->willReturn($repository);

        $repository->moveUp($category)->shouldBeCalled();

        $em->persist($category)->shouldBeCalled();
        $em->flush()->shouldBeCalled();

        $router->generate('fsi_admin_crud_list', Argument::withEntry('element', 'category'))
            ->shouldBeCalled()
            ->willReturn('sample-path');

        $response = $this->moveUpAction($element, $request);
        $response->shouldHaveType('Symfony\Component\HttpFoundation\RedirectResponse');
    }

    function it_moves_down_item_when_move_down_action_called(
        CRUDElement $element,
        Request $request,
        NestedTreeRepository $repository,
        \StdClass $category,
        Request $request,
        EntityManager $em,
        Router $router,
        DoctrineDataIndexer $indexer
    ) {
        $request->get('id')->willReturn(1);
        $indexer->getData(1)->willReturn($category);

        $element->getRepository()->willReturn($repository);

        $repository->moveDown($category)->shouldBeCalled();

        $em->persist($category)->shouldBeCalled();
        $em->flush()->shouldBeCalled();

        $router->generate('fsi_admin_crud_list', Argument::withEntry('element', 'category'))
            ->shouldBeCalled()
            ->willReturn('sample-path');

        $response = $this->moveDownAction($element, $request);
        $response->shouldHaveType('Symfony\Component\HttpFoundation\RedirectResponse');
    }

    function it_throws_runtime_exception_when_specified_entity_doesnt_exist(
        CRUDElement $element,
        Request $request,
        DoctrineDataIndexer $indexer
    ) {
        $request->get('id')->willReturn(666);
        $indexer->getData(666)->willThrow('FSi\Component\DataIndexer\Exception\RuntimeException');

        $this->shouldThrow('FSi\Component\DataIndexer\Exception\RuntimeException')
            ->duringMoveUpAction($element, $request);

        $this->shouldThrow('FSi\Component\DataIndexer\Exception\RuntimeException')
            ->duringMoveDownAction($element, $request);
    }
}
