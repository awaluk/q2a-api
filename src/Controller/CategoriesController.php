<?php

namespace Q2aApi\Controller;

use Q2aApi\Base\AbstractController;
use Q2aApi\Http\Request;
use Q2aApi\Http\Response;
use Q2aApi\Service\CategoriesService;

class CategoriesController extends AbstractController
{
    private $service;

    public function __construct(Request $request)
    {
        $this->service = new CategoriesService();
        parent::__construct($request);
    }

    public function list(): Response
    {
        $categories = $this->service->getAllCategories();
        $tree = $this->service->getCategoriesForGroup($categories);

        return $this->json($tree);
    }
}
