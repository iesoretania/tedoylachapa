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

use AppBundle\Entity\Model;
use AppBundle\Form\Type\ModelType;
use AppBundle\Repository\ModelRepository;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route("/modelos")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class ModelController extends Controller
{
    /**
     * @Route("/nuevo", name="model_form_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $model = new Model();
        $em->persist($model);

        return $this->indexAction($request, $translator, $model);
    }

    /**
     * @Route("/detalle/{code}", name="model_form_edit", methods={"GET", "POST"})
     */
    public function indexAction(Request $request, TranslatorInterface $translator, Model $model)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ModelType::class, $model);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // recuperar imagen si se ha cambiado
                /** @var File $filename */
                $file = $form->get('file_image')->getData();

                if ($file) {
                    // leer fichero, covertirlo en una imagen png de 300x300 y guardarla
                    $strm = fopen($file->getRealPath(), 'rb');
                    $image = imagecreatefromstring(stream_get_contents($strm));
                    $width = imagesx($image);
                    $tmp = imagecreatetruecolor(300, 300);
                    imagesavealpha($tmp, true);
                    imagecopyresampled($tmp, $image, 0, 0, 0, 0, 300, 300, $width, $width);
                    ob_start();
                    imagepng($tmp);
                    $data = ob_get_contents();
                    ob_end_clean();
                    $model->setImage($data);
                    imagedestroy($tmp);
                    imagedestroy($image);
                }
                $em->flush();
                $this->addFlash('success', $translator->trans('message.saved', [], 'model'));
                return $this->redirectToRoute('model_list');
            } catch (\Exception $e) {
                $this->addFlash('error', $translator->trans('message.error', [], 'model'));
                throw $e;
            }
        }

        $title = $translator->trans($model->getId() ? 'title.edit' : 'title.new', [], 'model');

        $breadcrumb = [
            $model->getId() ?
                ['fixed' => (string) $model->getCode()] :
                ['fixed' => $translator->trans('title.new', [], 'model')]
        ];

        return $this->render('model/form.html.twig', [
            'menu_path' => 'model_list',
            'breadcrumb' => $breadcrumb,
            'title' => $title,
            'model' => $model,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/listar/{page}", name="model_list", requirements={"page" = "\d+"},
     *     defaults={"page" = "1"}, methods={"GET"})
     */
    public function listAction(TranslatorInterface $translator, Request $request, $page)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();

        $queryBuilder
            ->select('m')
            ->from(Model::class, 'm')
            ->orderBy('m.code');

        $q = $request->get('q', null);
        if ($q) {
            $queryBuilder
                ->where('m.code LIKE :tq')
                ->orWhere('m.description LIKE :tq')
                ->setParameter('tq', '%'.$q.'%');
        }

        $adapter = new DoctrineORMAdapter($queryBuilder, false);
        $pager = new Pagerfanta($adapter);
        $pager
            ->setMaxPerPage($this->getParameter('page.size'))
            ->setCurrentPage($q ? 1 : $page);

        $title = $translator->trans('title.list', [], 'model');

        return $this->render('model/list.html.twig', [
            'title' => $title,
            'pager' => $pager,
            'q' => $q,
            'domain' => 'model'
        ]);
    }

    /**
     * @Route("/eliminar", name="model_delete", methods={"POST"})
     */
    public function deleteAction(
        Request $request,
        TranslatorInterface $translator,
        ModelRepository $modelRepository
    ) {
        $em = $this->getDoctrine()->getManager();

        $items = $request->request->get('items', []);
        if (count($items) === 0) {
            return $this->redirectToRoute('model_list');
        }

        $items = $modelRepository->findAllInListById($items);

        if ($request->get('confirm', '') === 'ok') {
            try {
                $modelRepository->deleteFromList($items);

                $em->flush();
                $this->addFlash('success', $translator->trans('message.deleted', [], 'model'));
            } catch (\Exception $e) {
                $this->addFlash('error', $translator->trans('message.delete_error', [], 'model'));
            }
            return $this->redirectToRoute('model_list');
        }

        $title = $translator->trans('title.delete', [], 'model');
        return $this->render('model/delete.html.twig', [
            'menu_path' => 'model_list',
            'breadcrumb' => [['fixed' => $title]],
            'title' => $title,
            'items' => $items
        ]);
    }
}
