<?php

namespace Lib\NTag;

class NTList
{
    private $NTag = null;


    function __construct()
    {
        $this->NTag = \Lib\NTag::this();
    }

    /**
     * List :: Create ul html tag
     * Parameter "tag" is the list type indicator (ex.: <s:_list  . . . tag="li" />)
     *
     * @param array $ret ©NeosTag data array
     * @return string|html
    */
    public function make($data)
    {
        if (!isset($data['data'])) {
            return '';
        }
        $v = $this->NTag->getVar($data['data']);
        if (!$v || !is_array($v)) {
            return '';
        }

        $tag = isset($data['tag']) ? $data['tag'] : 'li';
        $data = $this->NTag->clearData($data);

        //Tag UL and params. (class, id, etc)
        $o = '<ul';
        foreach ($data as $k => $val) {
            $o .= ' '.trim($k).'="'.trim($val).'"';
        }
        $o .= '>';
        //create list
        foreach ($v as $k => $val) {
            $o .= '<'.$tag.'>'.$val.'</'.$tag.'>';
        }
        return $o . '</ul>';
    }
}
