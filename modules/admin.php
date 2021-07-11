<?php

class api_admin
{
    public function admin_form()
    {
        $saved = false;
        if (qa_clicked('save')) {
            qa_opt('api_cors_origin', qa_post_text('cors_origin'));
            $saved = true;
        }

        return [
            'ok' => $saved ? qa_lang_html('q2a_api/admin_saved') : null,
            'fields' => [
                'input1' => [
                    'type' => 'text',
                    'label' => qa_lang_html('q2a_api/cors_origin') . ':',
                    'value' => qa_opt('api_cors_origin'),
                    'tags' => 'name="cors_origin"'
                ],
            ],
            'buttons' => [
                [
                    'label' => qa_lang_html('q2a_api/admin_save'),
                    'tags' => 'name="save"'
                ]
            ]
        ];
    }
}
