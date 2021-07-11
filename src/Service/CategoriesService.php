<?php

namespace Q2aApi\Service;

class CategoriesService
{
    public function getAllCategories(): array
    {
        return qa_db_read_all_assoc(qa_db_query_sub('SELECT * FROM ^categories ORDER BY `position`'));
    }

    public function getCategoriesForGroup(array $categories, int $parentId = null): array
    {
        $filtered = array_filter($categories, function ($category) use ($parentId) {
            return ($parentId === null && $category['parentid'] === null) || $parentId === (int)$category['parentid'];
        });
        $filtered = array_map(function ($category) {
            return [
                'id' => (int)$category['categoryid'],
                'title' => $category['title'],
                'slug' => $category['tags'],
                'path' => $category['backpath'],
                'description' => $category['content'],
                'position' => (int)$category['position'],
                'questionsCount' => (int)$category['qcount'],
            ];
        }, $filtered);

        foreach ($filtered as &$category) {
            $category['subcategories'] = $this->getCategoriesForGroup($categories, $category['id']);
        }

        return array_values($filtered);
    }
}
