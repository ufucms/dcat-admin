<?php

namespace Dcat\Admin\Grid\Displayers;

class ModalHtml extends Modal
{
    public function display($field = null, $icon = null)
    {
        if($field === null){
            $content = $this->row->content;
            $this->title = admin_trans_field('content');
        }else{
            $this->title = admin_trans_field($field);
            $content = $this->row->$field;
        }
        $icon ? $this->icon = $icon : null;
        return parent::display(function ($modal) use($content) {
            return view('admin::grid.displayer.content', compact('content'));
        });
    }
}
