<?php

namespace Q2aApi;

class StatisticsController extends AbstractController
{
    public function get(): Response
    {
        return $this->json(['data' => [
            'questions_count' => (int)qa_opt('cache_qcount'),
            'answers_count' => (int)qa_opt('cache_acount'),
            'comments_count' => (int)qa_opt('cache_ccount'),
            'users_count' => (int)qa_opt('cache_userpointscount'),
        ]]);
    }
}
