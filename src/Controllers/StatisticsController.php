<?php

namespace Q2aApi\Controllers;

use Q2aApi\Base\AbstractController;
use Q2aApi\Http\Response;

class StatisticsController extends AbstractController
{
    public function get(): Response
    {
        return $this->json([
            'questionsCount' => (int)qa_opt('cache_qcount'),
            'answersCount' => (int)qa_opt('cache_acount'),
            'commentsCount' => (int)qa_opt('cache_ccount'),
            'usersCount' => (int)qa_opt('cache_userpointscount'),
        ]);
    }
}
