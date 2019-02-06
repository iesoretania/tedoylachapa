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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1")
 * @Security("is_granted('ROLE_USER')")
 */
class ApiController extends Controller
{
    /**
     * @Route("/model/image/{code}", name="api_model_image", methods={"GET"})
     * @Cache(expires="+15 minutes", public=true)
     */
    public function modelImageAction(Model $model)
    {
        $callback = function () use ($model) {
            echo stream_get_contents($model->getImage());
        };
        $response = new StreamedResponse($callback);
        $response->headers->set('Content-Type', 'image/png');
        return $response;
    }
}
