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

use AppBundle\Entity\ReferenceReception;
use AppBundle\Form\Type\ReferenceReceptionType;
use AppBundle\Repository\ReferenceReceptionRepository;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route("/referencias/entradas")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class ReferenceReceptionController extends Controller
{
    /**
     * @Route("/nueva", name="reference_reception_form_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $referenceReception = new ReferenceReception();
        $referenceReception
            ->setAddedBy($this->getUser())
            ->setDate(new \DateTime());

        $em->persist($referenceReception);

        return $this->indexAction($request, $translator, $referenceReception);
    }

    /**
     * @Route("/detalle/{id}", name="reference_reception_form_edit", methods={"GET", "POST"})
     */
    public function indexAction(Request $request, TranslatorInterface $translator, ReferenceReception $referenceReception)
    {
        $em = $this->getDoctrine()->getManager();

        $prevReference = $referenceReception->getReference();
        $prevQuantity = $referenceReception->getQuantity();

        $form = $this->createForm(ReferenceReceptionType::class, $referenceReception);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if ($prevReference) {
                    $prevReference->setStock($prevReference->getStock() - $prevQuantity);
                }
                if ($referenceReception->getReference()) {
                    $referenceReception->getReference()->setStock(
                        $referenceReception->getReference()->getStock() + $referenceReception->getQuantity()
                    );
                }
                $em->flush();
                $routeName = $request->get('submit-repeat', null) === '' ? 'reference_reception_form_new' : 'reference_reception';
                $this->addFlash('success', $translator->trans('message.saved', [], 'reference_reception'));
                return $this->redirectToRoute($routeName);
            } catch (\Exception $e) {
                $this->addFlash('error', $translator->trans('message.error', [], 'reference_reception'));
            }
        }

        $title = $translator->trans($referenceReception->getId() ? 'title.edit' : 'title.new', [], 'reference_reception');

        $breadcrumb = [
            ['fixed' => $translator->trans('form.reception', [], 'reference')],
            $referenceReception->getId() ?
                ['fixed' => $translator->trans('title.edit', [], 'reference_reception')] :
                ['fixed' => $translator->trans('title.new', [], 'reference_reception')]
        ];

        return $this->render('reference/reception_form.html.twig', [
            'menu_path' => 'reference',
            'breadcrumb' => $breadcrumb,
            'title' => $title,
            'reference_reception' => $referenceReception,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/listar/{page}", name="reference_reception", requirements={"page" = "\d+"},
     *     defaults={"page" = "1"}, methods={"GET"})
     */
    public function listAction(TranslatorInterface $translator, Request $request, $page)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();

        $queryBuilder
            ->select('r')
            ->from(ReferenceReception::class, 'r')
            ->join('r.reference', 're')
            ->orderBy('r.date', 'DESC');

        $q = $request->get('q', null);
        if ($q) {
            $queryBuilder
                ->where('re.code LIKE :tq')
                ->orWhere('re.description LIKE :tq')
                ->orWhere('r.description LIKE :tq')
                ->setParameter('tq', '%'.$q.'%');
        }

        $adapter = new DoctrineORMAdapter($queryBuilder, false);
        $pager = new Pagerfanta($adapter);
        $pager
            ->setMaxPerPage($this->getParameter('page.size'))
            ->setCurrentPage($q ? 1 : $page);

        $title = $translator->trans('title.list', [], 'reference_reception');
        $breadcrumb = [
            ['fixed' => $translator->trans('form.reception', [], 'reference')]
        ];

        return $this->render('reference/reception_list.html.twig', [
            'menu_path' => 'reference',
            'breadcrumb' => $breadcrumb,
            'title' => $title,
            'pager' => $pager,
            'q' => $q,
            'domain' => 'reference_reception'
        ]);
    }

    /**
     * @Route("/eliminar", name="reference_reception_delete", methods={"POST"})
     */
    public function deleteAction(
        Request $request,
        TranslatorInterface $translator,
        ReferenceReceptionRepository $referenceReceptionRepository
    ) {
        $em = $this->getDoctrine()->getManager();

        $items = $request->request->get('items', []);
        if (count($items) === 0) {
            return $this->redirectToRoute('reference_reception');
        }

        $items = $referenceReceptionRepository->findAllInListById($items);

        if ($request->get('confirm', '') === 'ok') {
            try {
                foreach ($items as $referenceReception) {
                    $referenceReception->getReference()->setStock(
                        $referenceReception->getReference()->getStock() - $referenceReception->getQuantity()
                    );
                }
                $referenceReceptionRepository->deleteFromList($items);

                $em->flush();
                $this->addFlash('success', $translator->trans('message.deleted', [], 'reference_reception'));
            } catch (\Exception $e) {
                $this->addFlash('error', $translator->trans('message.delete_error', [], 'reference_reception'));
            }
            return $this->redirectToRoute('reference_reception');
        }

        $title = $translator->trans('title.delete', [], 'reference_reception');
        return $this->render('reference/reception_delete.html.twig', [
            'menu_path' => 'reference',
            'breadcrumb' => [
                ['fixed' => $translator->trans('form.reception', [], 'reference')],
                ['fixed' => $title]
            ],
            'title' => $title,
            'items' => $items
        ]);
    }
}
