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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

class PersonalDataController extends Controller
{
    /**
     * @Route("/datos", name="personal_data", methods={"GET", "POST"})
     */
    public function userProfileFormAction(TranslatorInterface $translator, Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user, [
            'own' => true,
            'admin' => $user->isAdministrator()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $passwordSubmitted = $this->processPasswordChanges($form, $user);
            $message = $translator->trans(
                $passwordSubmitted ? 'message.password_changed' : 'message.saved',
                [],
                'user'
            );

            try {
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', $message);
                return $this->redirectToRoute('frontpage');
            } catch (\Exception $e) {
                $this->addFlash('error', $translator->trans('message.error', [], 'user'));
            }
        }

        return $this->render('user/personal_data_form.html.twig', [
            'menu_path' => 'frontpage',
            'breadcrumb' => [
                ['caption' => 'menu.personal_data']
            ],
            'title' => $translator->trans('user.data', [], 'layout'),
            'form' => $form->createView(),
            'user' => $user,
            'last_url' => $this->generateUrl('frontpage')
        ]);
    }

    /**
     * Checks if a password/email change has been requested and process it
     * @param FormInterface $form
     * @param User $user
     * @return bool
     */
    private function processPasswordChanges(
        FormInterface $form,
        User $user
    ) {
        // Si es solicitado, cambiar la contraseña
        $passwordSubmitted = ($form->has('changePassword') &&
                $form->get('changePassword') instanceof SubmitButton) && $form->get('changePassword')->isClicked();
        if ($passwordSubmitted) {
            $user->setPassword($this->get('security.password_encoder')
                ->encodePassword($user, $form->get('newPassword')->get('first')->getData()));
        }
        return $passwordSubmitted;
    }
}
