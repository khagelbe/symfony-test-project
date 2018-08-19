<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    /**
     * Lists all available categories
     *
     * @return JsonResponse
     */
    public function listAction(): JsonResponse
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        $result = [];

        foreach ($categories as $category) {
            $result[] = [
                'name' => $category->getName()
            ];
        }

        return new JsonResponse($result, Response::HTTP_OK);
    }
}
