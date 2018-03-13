<?php

namespace Lib\NTag;

class NTPlugin
{
    private $NTag = null;


    function __construct()
    {
        $this->NTag = \Lib\NTag::this();
    }

    /**
     * Plugin :: insert content block
     * Parameter "name" is the name of block;
     *
     * @param array $ret ©NeosTag data array
     * @return string|html
    */
    public function make($data)
    {
        if (!isset($data['load'])) {
            return '';
        }
        
        $plugin = $this->NTag->getPlugin(trim($data['load']));
        if ($plugin == false || !class_exists($plugin)) {
            return '';
        }

        // instantiate and renderize
        $attributes = $this->NTag->clearData($data);
        $att = '[';
        foreach ($attributes as $key => $val) {
            $att .= "'$key'=>'$val',";
        }
        $att = substr($att, 0, -1).']';

        // add plugin call
        $data['-content-'] = '<?php echo (new '.$plugin.')->render('.$att.')?>';

        // return data
        return $data;
    }
}
