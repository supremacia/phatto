<?php

namespace Lib\NTag;

class NTBlock
{
    private $Html = null;


    function __construct()
    {
        $this->Html = \Lib\Html::this();
    }

    /**
     * _block :: insert content block
     * Parameter "name" is the name of block;
     *
     * @param array $ret ©NeosTag data array
     * @return string|html
    */
    public function make($data)
    {
        if (!isset($data['data'])) {
            return '';
        }
        
        $data['-content-'] .= $this->Html->getBlock(trim($data['data']));
        if ($data['-content-'] == false) {
            return '';
        }

        unset($data['data']);
        return $this->Html->setAttributes($data);
    }
}
