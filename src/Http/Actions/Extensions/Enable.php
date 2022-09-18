<?php

namespace Dcat\Admin\Http\Actions\Extensions;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid\RowAction;

class Enable extends RowAction
{
    public function title()
    {
        return sprintf('<b>%s</b>', trans('admin.enable'));
    }

    public function handle()
    {
        Admin::extension()->enable($this->getKey());

        return $this
            ->response()
            ->location()
            ->success(trans('admin.update_succeeded'));
    }
}
