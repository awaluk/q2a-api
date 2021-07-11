<?php

namespace Q2aApi\Controller;

use Q2aApi\Base\AbstractController;
use Q2aApi\Http\Response;
use Q2aApi\Service\CategoriesService;

class CategoriesController extends AbstractController
{
    public function list(): Response
    {
        $service = new CategoriesService();
        $categories = $service->getAllCategories();
        $tree = $service->getCategoriesForGroup($categories);

        return $this->json($tree);
    }
}
