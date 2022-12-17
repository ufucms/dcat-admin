<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Widgets\Markdown;

class ModalMarkdown extends Modal
{
    public function display($field = null, $icon = null)
    {
        if($field === null){
            $content = Markdown::make($this->row->content);
            $this->title = admin_trans_field('content');
        }else{
            $this->title = admin_trans_field($field);
            $content = Markdown::make($this->row->$field);
        }
        $icon ? $this->icon = $icon : null;
        return parent::display(function ($modal) use($content) {
            return view('admin::grid.displayer.content', compact('content'));
        });
    }
}
