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

use AppBundle\Entity\Invoice;
use AppBundle\Entity\InvoiceLine;
use AppBundle\Form\Type\InvoiceLineType;
use AppBundle\Repository\InvoiceRepository;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route("/pedidos")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class InvoiceController extends Controller
{
    /**
     * @Route("/nuevo", name="invoice_form_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $invoice = new Invoice();
        $invoice
            ->setCreatedBy($this->getUser());

        $em->persist($invoice);

        return $this->indexAction($request, $translator, $invoice);
    }

    /**
     * @Route("/detalle/{id}", name="invoice_form_edit", methods={"GET", "POST"})
     */
    public function indexAction(Request $request, TranslatorInterface $translator, Invoice $invoice)
    {
        $em = $this->getDoctrine()->getManager();

        $invoiceLine = new InvoiceLine();
        $invoiceLine
            ->setInvoice($invoice)
            ->setOrderNr(count($invoice->getLines()) + 1)
            ->setQuantity(0)
            ->setRate(0)
            ->setDiscount(0);

        $em->persist($invoiceLine);

        $formLine = $this->createForm(InvoiceLineType::class, $invoiceLine);

        $formLine->handleRequest($request);

        if ($formLine->isSubmitted() && $formLine->isValid()) {
            try {
                if ($request->get('finalize', null) === '') {
                    $invoice->setFinalizedOn(new \DateTime());
                    $em->remove($invoiceLine);
                }
                $em->flush();
                $this->addFlash('success', $translator->trans('message.saved', [], 'invoice'));
                if ($request->get('finalize', null) === '') {
                    return $this->redirectToRoute('invoice');
                }
                return $this->redirectToRoute('invoice_form_edit', ['id' => $invoice->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('error', $translator->trans('message.error', [], 'invoice'));
            }
        }

        $title = $translator->trans($invoice->getId() ? 'title.edit' : 'title.new', [], 'invoice');

        if ($invoice->getId()) {
            $title .= ' ' . $invoice->getCode();
        }

        $breadcrumb = [
            $invoice->getId() ?
                ['fixed' => (string) $invoice->getCode()] :
                ['fixed' => $translator->trans('title.new', [], 'invoice')]
        ];

        return $this->render('invoice/form.html.twig', [
            'menu_path' => 'invoice',
            'breadcrumb' => $breadcrumb,
            'title' => $title,
            'form_line' => $formLine->createView(),
            'invoice' => $invoice,
            'new' => $invoice->getId() === null
        ]);
    }

    /**
     * @Route("/listar/{page}", name="invoice", requirements={"page" = "\d+"},
     *     defaults={"page" = "1"}, methods={"GET"})
     */
    public function listAction(TranslatorInterface $translator, Request $request, $page)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();

        $queryBuilder
            ->select('i')
            ->from(Invoice::class, 'i')
            ->join('i.createdBy', 'u')
            ->orderBy('i.createdBy', 'DESC');

        $q = $request->get('q', null);
        if ($q) {
            $queryBuilder
                ->where('u.firstName LIKE :tq')
                ->orWhere('u.lastName LIKE :tq')
                ->orWhere('i.notes LIKE :tq')
                ->setParameter('tq', '%'.$q.'%');
        }

        $adapter = new DoctrineORMAdapter($queryBuilder, false);
        $pager = new Pagerfanta($adapter);
        $pager
            ->setMaxPerPage($this->getParameter('page.size'))
            ->setCurrentPage($q ? 1 : $page);

        $title = $translator->trans('title.list', [], 'invoice');

        return $this->render('invoice/list.html.twig', [
            'title' => $title,
            'pager' => $pager,
            'q' => $q,
            'domain' => 'invoice'
        ]);
    }

    /**
     * @Route("/eliminar", name="invoice_delete", methods={"POST"})
     */
    public function deleteAction(
        Request $request,
        TranslatorInterface $translator,
        InvoiceRepository $invoiceRepository
    ) {
        $em = $this->getDoctrine()->getManager();

        $items = $request->request->get('items', []);
        if (count($items) === 0) {
            return $this->redirectToRoute('invoice');
        }

        $items = $invoiceRepository->findAllInListById($items);

        if ($request->get('confirm', '') === 'ok') {
            try {
                $invoiceRepository->deleteFromList($items);

                $em->flush();
                $this->addFlash('success', $translator->trans('message.deleted', [], 'invoice'));
            } catch (\Exception $e) {
                $this->addFlash('error', $translator->trans('message.delete_error', [], 'invoice'));
            }
            return $this->redirectToRoute('invoice');
        }

        $title = $translator->trans('title.delete', [], 'invoice');
        return $this->render('invoice/delete.html.twig', [
            'menu_path' => 'invoice',
            'breadcrumb' => [['fixed' => $title]],
            'title' => $title,
            'items' => $items
        ]);
    }
}
