<?php

namespace Dcat\Admin\Grid\Displayers;

class ModalList extends Modal
{
    public function display($titles = [], $field = null, $content = null, $icon = null)
    {
        if($field === null){
            $data = $this->toArray($this->row->data);
            $this->title = admin_trans_field('data');
        }else{
            $this->title = admin_trans_field($field);
            $data = $this->toArray($this->row->$field);
        }
        $icon ? $this->icon = $icon : null;
        return parent::display(function ($modal) use($titles, $data, $content) {
            return view('admin::grid.displayer.list', compact('titles', 'data', 'content'));
        });
    }

    // 字段数据转数组格式
    protected function toArray($data)
    {
        if(is_array($data)){
            return $data;
        }else{
            return (array) json_decode($data, true);
        }

    }
}
