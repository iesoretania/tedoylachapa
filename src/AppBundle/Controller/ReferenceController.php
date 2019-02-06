<?php
/*
  Copyright (C) 2019: Luis Ramón López López

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with this program.  If not, see [http://www.gnu.org/licenses/].
*/

namespace AppBundle\Controller;

use AppBundle\Entity\Reference;
use AppBundle\Form\Type\ReferenceType;
use AppBundle\Repository\ReferenceRepository;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route("/referencias")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class ReferenceController extends Controller
{
    /**
     * @Route("/nueva", name="reference_form_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $reference = new Reference();
        $em->persist($reference);

        return $this->indexAction($request, $translator, $reference);
    }

    /**
     * @Route("/detalle/{code}", name="reference_form_edit", methods={"GET", "POST"})
     */
    public function indexAction(Request $request, TranslatorInterface $translator, Reference $reference)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ReferenceType::class, $reference);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em->flush();
                $this->addFlash('success', $translator->trans('message.saved', [], 'reference'));
                return $this->redirectToRoute('reference');
            } catch (\Exception $e) {
                $this->addFlash('error', $translator->trans('message.error', [], 'reference'));
            }
        }

        $title = $translator->trans($reference->getId() ? 'title.edit' : 'title.new', [], 'reference');

        $breadcrumb = [
            $reference->getId() ?
                ['fixed' => (string) $reference->getCode()] :
                ['fixed' => $translator->trans('title.new', [], 'reference')]
        ];

        return $this->render('reference/form.html.twig', [
            'menu_path' => 'reference',
            'breadcrumb' => $breadcrumb,
            'title' => $title,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/listar/{page}", name="reference", requirements={"page" = "\d+"},
     *     defaults={"page" = "1"}, methods={"GET"})
     */
    public function listAction(TranslatorInterface $translator, Request $request, $page)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();

        $queryBuilder
            ->select('r')
            ->from(Reference::class, 'r')
            ->orderBy('r.code');

        $q = $request->get('q', null);
        if ($q) {
            $queryBuilder
                ->where('r.code LIKE :tq')
                ->orWhere('r.description LIKE :tq')
                ->setParameter('tq', '%'.$q.'%');
        }

        $adapter = new DoctrineORMAdapter($queryBuilder, false);
        $pager = new Pagerfanta($adapter);
        $pager
            ->setMaxPerPage($this->getParameter('page.size'))
            ->setCurrentPage($q ? 1 : $page);

        $title = $translator->trans('title.list', [], 'reference');

        return $this->render('reference/list.html.twig', [
            'title' => $title,
            'pager' => $pager,
            'q' => $q,
            'domain' => 'reference'
        ]);
    }

    /**
     * @Route("/eliminar", name="reference_delete", methods={"POST"})
     */
    public function deleteAction(
        Request $request,
        TranslatorInterface $translator,
        ReferenceRepository $referenceRepository
    ) {
        $em = $this->getDoctrine()->getManager();

        $items = $request->request->get('items', []);
        if (count($items) === 0) {
            return $this->redirectToRoute('reference');
        }

        $items = $referenceRepository->findAllInListById($items);

        if ($request->get('confirm', '') === 'ok') {
            try {
                $referenceRepository->deleteFromList($items);

                $em->flush();
                $this->addFlash('success', $translator->trans('message.deleted', [], 'reference'));
            } catch (\Exception $e) {
                $this->addFlash('error', $translator->trans('message.delete_error', [], 'reference'));
            }
            return $this->redirectToRoute('reference');
        }

        $title = $translator->trans('title.delete', [], 'reference');
        return $this->render('reference/delete.html.twig', [
            'menu_path' => 'reference',
            'breadcrumb' => [['fixed' => $title]],
            'title' => $title,
            'items' => $items
        ]);
    }
}
