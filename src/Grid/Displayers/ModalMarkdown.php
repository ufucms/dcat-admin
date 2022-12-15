<?php

namespace Dcat\Admin\Grid\Displayers;

use GrahamCampbell\Markdown\Facades\Markdown;

class ModalMarkdown extends Modal
{
    public function display($field = null, $icon = null)
    {
        if($field === null){
            $content = Markdown::convert($this->row->content)->getContent();
            $this->title = admin_trans_field('content');
        }else{
            $this->title = admin_trans_field($field);
            $content = Markdown::convert($this->row->$field)->getContent();
        }
        $icon ? $this->icon = $icon : null;
        return parent::display(function ($modal) use($content) {
            return view('admin::grid.displayer.content', compact('content'));
        });
    }
}
