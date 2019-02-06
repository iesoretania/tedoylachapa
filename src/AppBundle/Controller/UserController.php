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

use AppBundle\Entity\User;
use AppBundle\Form\Type\UserType;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route("/usuarios")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class UserController extends Controller
{
    /**
     * @Route("/nuevo", name="user_form_new", methods={"GET", "POST"})
     * @Route("/{id}", name="user_form_edit", requirements={"id" = "\d+"}, methods={"GET", "POST"})
     */
    public function indexAction(Request $request, TranslatorInterface $translator, User $localUser = null)
    {
        $em = $this->getDoctrine()->getManager();

        if (null === $localUser) {
            $localUser = new User();
            $em->persist($localUser);
        }

        $form = $this->createForm(UserType::class, $localUser, [
            'own' => $this->getUser()->getId() === $localUser->getId(),
            'admin' => $this->getUser()->isAdministrator(),
            'new' => $localUser->getId() === null
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message = $this->processPasswordChange($localUser, $form);

            try {
                $em->flush();
                $this->addFlash('success', $message);
                return $this->redirectToRoute('user');
            } catch (\Exception $e) {
                $this->addFlash('error', $translator->trans('message.error', [], 'user'));
            }
        }

        $title = $translator->trans($localUser->getId() ? 'title.edit' : 'title.new', [], 'user');

        $breadcrumb = [
            $localUser->getId() ?
                ['fixed' => (string) $localUser] :
                ['fixed' => $translator->trans('title.new', [], 'user')]
        ];

        return $this->render('user/form.html.twig', [
            'menu_path' => 'user',
            'breadcrumb' => $breadcrumb,
            'title' => $title,
            'form' => $form->createView(),
            'user' => $localUser
        ]);
    }

    /**
     * @Route("/listar/{page}", name="user", requirements={"page" = "\d+"},
     *     defaults={"page" = "1"}, methods={"GET"})
     */
    public function listAction(TranslatorInterface $translator, Request $request, $page)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();

        $queryBuilder
            ->select('u')
            ->from(User::class, 'u')
            ->orderBy('u.lastName')
            ->addOrderBy('u.firstName');

        $q = $request->get('q', null);
        if ($q) {
            $queryBuilder
                ->where('u.id = :q')
                ->orWhere('u.loginUsername LIKE :tq')
                ->orWhere('u.firstName LIKE :tq')
                ->orWhere('u.lastName LIKE :tq')
                ->setParameter('tq', '%'.$q.'%')
                ->setParameter('q', $q);
        }

        $adapter = new DoctrineORMAdapter($queryBuilder, false);
        $pager = new Pagerfanta($adapter);
        $pager
            ->setMaxPerPage($this->getParameter('page.size'))
            ->setCurrentPage($q ? 1 : $page);

        $title = $translator->trans('title.list', [], 'user');

        return $this->render('user/list.html.twig', [
            'title' => $title,
            'pager' => $pager,
            'q' => $q,
            'domain' => 'user'
        ]);
    }

    /**
     * @Route("/eliminar", name="user_delete", methods={"POST"})
     */
    public function deleteAction(Request $request, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $em->createQueryBuilder();

        $items = $request->request->get('users', []);
        if (count($items) === 0) {
            return $this->redirectToRoute('user');
        }

        $users = $queryBuilder
            ->select('u')
            ->from(User::class, 'u')
            ->where('u IN (:items)')
            ->andWhere('u != :current')
            ->setParameter('items', $items)
            ->setParameter('current', $this->getUser())
            ->orderBy('u.firstName')
            ->addOrderBy('u.lastName')
            ->getQuery()
            ->getResult();

        if ($request->get('confirm', '') === 'ok') {
            try {
                $em->createQueryBuilder()
                    ->delete(User::class, 'u')
                    ->where('u IN (:items)')
                    ->setParameter('items', $items)
                    ->getQuery()
                    ->execute();

                $em->flush();
                $this->addFlash('success', $translator->trans('message.deleted', [], 'user'));
            } catch (\Exception $e) {
                $this->addFlash('error', $translator->trans('message.delete_error', [], 'user'));
            }
            return $this->redirectToRoute('user');
        }

        return $this->render('user/delete.html.twig', [
            'menu_path' => 'user',
            'breadcrumb' => [['fixed' => $translator->trans('title.delete', [], 'user')]],
            'title' => $translator->trans('title.delete', [], 'user'),
            'users' => $users
        ]);
    }

    /**
     * @param User $user
     * @param FormInterface $form
     * @return string
     */
    private function processPasswordChange(User $user, FormInterface $form)
    {
        // Si es solicitado, cambiar la contraseña
        $passwordSubmit = $form->get('changePassword');
        if (($passwordSubmit instanceof SubmitButton) && $passwordSubmit->isClicked()) {
            $user->setPassword($this->container->get('security.password_encoder')
                ->encodePassword($user, $form->get('newPassword')->get('first')->getData()));
            $message = $this->get('translator')->trans('message.password_changed', [], 'user');
        } else {
            $message = $this->get('translator')->trans('message.saved', [], 'user');
        }
        return $message;
    }
}
