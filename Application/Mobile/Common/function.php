<?php
//传分类id获取分类名称
function getCateName($cat_id){
    switch ($cat_id) {
        case '88':
            return '快讯';
            break;
        case '89':
            return '涨停板';
            break;
        case '90':
            return '精华版';
            break;
        case '91':
            return '实时内情';
            break;
        case '92':
            return '新声TV';
            break;
        case '93':
            return '三板客';
            break;
        case '94':
            return '深度';
            break;
        case '95':
            return '酷公司';
            break;
        case '96':
            return 'CEO新声';
            break;
        case '97':
            return '互联网金融';
            break;
        case '98':
            return 'O2O智能硬件';
            break;
        case '99':
            return '创业投资';
            break;
    }
}