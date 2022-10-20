<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form\NestedForm;

class Icon extends Text
{
    public static $js = '@fontawesome-iconpicker';
    public static $css = '@fontawesome-iconpicker';

    public function render()
    {
        $this->addScript();

        $this->prepend("<i class='fa {$this->value()}'>&nbsp;</i>")
            ->defaultAttribute('autocomplete', 'off')
            ->defaultAttribute('style', 'width: 120px; flex:none;')
            ->defaultAttribute('data-table-item-index', NestedForm::DEFAULT_KEY_NAME);
        return parent::render();
    }

    protected function addScript()
    {
        $this->script = <<<JS
setTimeout(function () {
    var formId = '#{$this->getFormElementId()}';
    var domId = formId + ' .field_table_icon';
    $(document).off('focus', domId).on('focus', domId, function (e) {
        var field = $(this),
            index = $(this).attr('data-table-item-index') - 1,
            parent = field.parents('.form-field'),
            showIcon = function (icon) {
                parent.find('.input-group-prepend .input-group-text').html('<i class="' + icon + '"></i>');
            };
        field.iconpicker({placement:'bottomLeft', animation: false});
        
        parent.find('.iconpicker-item').on('click', function (e) {
            showIcon($(this).find('i').attr('class'));
        });
        
        field.on('keyup', function (e) {
            var val = $(this).val();
            
            if (val.indexOf('fa-') !== -1) {
                if (val.indexOf('fa ') === -1) {
                    val = 'fa ' + val;
                }
            }
            
            showIcon(val);
        });
    });
}, 1);


setTimeout(function () {
    var domId = '{$this->getElementClassSelector()}';
    var field = $(domId),
        parent = field.parents('.form-field'),
        showIcon = function (icon) {
            parent.find('.input-group-prepend .input-group-text').html('<i class="' + icon + '"></i>');
        };
    field.iconpicker({placement:'bottomLeft', animation: false});
    
    parent.find('.iconpicker-item').on('click', function (e) {
        showIcon($(this).find('i').attr('class'));
    });
    
    field.on('keyup', function (e) {
        var val = $(this).val();
        
        if (val.indexOf('fa-') !== -1) {
            if (val.indexOf('fa ') === -1) {
                val = 'fa ' + val;
            }
        }
        
        showIcon(val);
    });
}, 1);
JS;
    }
}
