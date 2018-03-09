<?php

namespace Lib\NTag;

class NTSelect
{
    private $NTag = null;


    function __construct()
    {
        $this->NTag = \Lib\NTag::this();
    }

    /**
     * Create <select> HTML tag
     * configuration:     *
     *
     *       exemplo:
     *
     *       $select = ['teste'=>['1'=>'Valor 1','2'=>'Valor 2','3'=>'Valor 3','-default-'=>'2']]
     *       $NTag->val('varName',$select);
     *
     *
     *       ---- in HTML file
     *       <x::select data="varName" ...some attributes />
     *
     * @param array $ret ©NeosTag data array
     * @return string|html
    */
    public function make($data)
    {
        if (!isset($data['data'])) {
            return '';
        }
        
        $var = $this->NTag->getVar($data['data']);
        if (!$var) {
            return false;
        }

        $default = isset($var['-default-']) ? $var['-default-'] : '0';
        unset($var['-default-']);

        $o = '';
        foreach ($var as $k => $v) {
            $o .= '<option value="'.$k.'"'.($default == $k ? ' selected' :'').'>'.$v.'</option>';
        }
        $data['-content-'] = $o;
        $data['tag'] = 'select';
        return $this->NTag->setAttributes($data);
    }
}
