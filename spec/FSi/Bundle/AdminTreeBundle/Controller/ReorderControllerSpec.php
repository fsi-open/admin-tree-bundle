<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\FSi\Bundle\AdminTreeBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use FSi\Component\DataIndexer\DoctrineDataIndexer;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use FSi\Bundle\AdminBundle\Doctrine\Admin\CRUDElement;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

class ReorderControllerSpec extends ObjectBehavior
{
    function let(
        Router $router,
        CRUDElement $element,
        DoctrineDataIndexer $indexer,
        ObjectManager $om,
        NestedTreeRepository $repository,
        Request $request,
        ParameterBag $query
    ) {
        $request->query = $query;
        $element->getId()->willReturn('category');
        $element->getDataIndexer()->willReturn($indexer);
        $element->getObjectManager()->willReturn($om);
        $element->getRepository()->willReturn($repository);
        $element->getRoute()->willReturn('fsi_admin_crud_list');
        $element->getRouteParameters()->willReturn(['element' => 'category']);

        $this->beConstructedWith($router);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('FSi\Bundle\AdminTreeBundle\Controller\ReorderController');
    }

    function it_moves_up_item_when_move_up_action_called(
        CRUDElement $element,
        NestedTreeRepository $repository,
        \stdClass $category,
        ObjectManager $om,
        Router $router,
        DoctrineDataIndexer $indexer,
        Request $request
    ) {
        $indexer->getData(1)->willReturn($category);

        $repository->moveUp($category)->shouldBeCalled();

        $om->flush()->shouldBeCalled();

        $router->generate('fsi_admin_crud_list', Argument::withEntry('element', 'category'))
            ->willReturn('sample-path');

        $response = $this->moveUpAction($element, 1, $request);
        $response->shouldHaveType('Symfony\Component\HttpFoundation\RedirectResponse');
        $response->getTargetUrl()->shouldReturn('sample-path');
    }

    function it_moves_down_item_when_move_down_action_called(
        CRUDElement $element,
        NestedTreeRepository $repository,
        \stdClass $category,
        ObjectManager $om,
        Router $router,
        DoctrineDataIndexer $indexer,
        Request $request
    ) {
        $indexer->getData(1)->willReturn($category);

        $repository->moveDown($category)->shouldBeCalled();

        $om->flush()->shouldBeCalled();

        $router->generate('fsi_admin_crud_list', Argument::withEntry('element', 'category'))
            ->willReturn('sample-path');

        $response = $this->moveDownAction($element, 1, $request);
        $response->shouldHaveType('Symfony\Component\HttpFoundation\RedirectResponse');
        $response->getTargetUrl()->shouldReturn('sample-path');
    }

    function it_throws_runtime_exception_when_specified_entity_doesnt_exist(
        CRUDElement $element,
        DoctrineDataIndexer $indexer,
        Request $request
    ) {
        $indexer->getData(666)->willThrow('FSi\Component\DataIndexer\Exception\RuntimeException');

        $this->shouldThrow('FSi\Component\DataIndexer\Exception\RuntimeException')
            ->duringMoveUpAction($element, 666, $request);

        $this->shouldThrow('FSi\Component\DataIndexer\Exception\RuntimeException')
            ->duringMoveDownAction($element, 666, $request);
    }

    function it_throws_exception_when_entity_doesnt_have_correct_repository(
        CRUDElement $element,
        EntityRepository $repository,
        DoctrineDataIndexer $indexer,
        \stdClass $category,
        Request $request
    ) {
        $indexer->getData(666)->willReturn($category);
        $element->getRepository()->willReturn($repository);

        $this->shouldThrow('\InvalidArgumentException')
            ->duringMoveUpAction($element, 666, $request);

        $this->shouldThrow('\InvalidArgumentException')
            ->duringMoveDownAction($element, 666, $request);
    }

    function it_redirects_to_redirect_uri_parameter_after_operation(
        CRUDElement $element,
        DoctrineDataIndexer $indexer,
        \stdClass $category,
        Request $request,
        ParameterBag $query
    ) {
        $query->get('redirect_uri')->willReturn('some_redirect_uri');
        $indexer->getData(1)->willReturn($category);

        $response = $this->moveUpAction($element, 1, $request);
        $response->shouldHaveType('Symfony\Component\HttpFoundation\RedirectResponse');
        $response->getTargetUrl()->shouldReturn('some_redirect_uri');

        $response = $this->moveDownAction($element, 1, $request);
        $response->shouldHaveType('Symfony\Component\HttpFoundation\RedirectResponse');
        $response->getTargetUrl()->shouldReturn('some_redirect_uri');
    }
}
