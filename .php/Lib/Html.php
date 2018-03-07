<?php

/**
 * Lib\Html
 * PHP version 7
 *
 * @category  Html
 * @package   Library
 * @author    Bill Rocha <prbr@ymail.com>
 * @copyright 2018 Bill Rocha <http://google.com/+BillRocha>
 * @license   <https://opensource.org/licenses/MIT> MIT
 * @version   GIT: 0.0.2
 * @link      Site <https://phatto.ga>
 */
namespace Lib;

/**
 * Html Class
 *
 * @category Html
 * @package  Library
 * @author   Bill Rocha <prbr@ymail.com>
 * @license  <https://opensource.org/licenses/MIT> MIT
 * @link     Site <https://phatto.ga>
 */
class Html
{
    private $name =             '';
    private $mode =             'dev'; //pro|dev
    private $cacheTime =        21600; // 6 hours of life
    private $cacheFile =        'default.html';

    private $pathHtml =         null;
    private $pathCache =        null;
    private $pathWww =          null;
    private $pathStyle =        null;
    private $pathScript =       null;
    private $url =              null;
    private $request =          null;

    //Html parts
    private $header =           null;
    private $body =             [];
    private $footer =           null;

    private $styles =           [];
    private $scripts =          [];
    private $forceCompress =    false;

    //Html template processors
    private $blade =            false;
    private $nTag =             true;

    private static $values =    [];
    private static $node =      null;

    private $jsvalues =         [];
    private $content =          '';
    private $tag =              'x:';


    /**
     * Construct of Doc
     */
    public function __construct(
        $name = null,
        $mode = null
    ) {
        if (class_exists('\Config\Html')) {
            foreach ($this as $key=>$value) {
                if (isset(\Config\Html::$$key)) {
                    $this->$key = \Config\Html::$$key;
                }
            }
        }

        if ($this->url === null) {
            $this->url = defined('_URL')  ? _URL  : './';
        }

        if ($name !== null) {
            $this->name =   $name;
        }

        if ($mode !== null) {
            $this->mode =   $mode;
        }

        //Saving this object in static node (for future static access)
        if (!is_object(static::$node)) {
            static::$node = $this;
        }
    }


    /**
     * Singleton instance
     *
     */
    public static function this()
    {
        if (is_object(static::$node)) {
            return static::$node;
        }
        //else...
        list($name, $mode) = array_merge(func_get_args(), [null, null]);
        return static::$node = new static($name, $mode);
    }

    /** Html template processor: Blade
     * 
    public function setBlade(bool $blade = true)
    {
        $this->blade = $blade;
        return $this;
    }

    /** Html template processor: NeosTag
     * 
    public function setNtag(bool $ntag = true)
    {
        $this->nTag = $ntag;
        return $this;
    }

    public function setPathHtml($val)
    {
        $this->pathHtml = rtrim($val, '\\/ ');
        return $this;
    }

    public function setPathWww(string $val)
    {
        $this->pathWww = rtrim($val, '\\/ ');
        ;
        return $this;
    }

    public function setPathScript(string $val)
    {
        $this->pathScript = rtrim($val, '\\/ ');
        ;
        return $this;
    }

    public function setPathStyle(string $val)
    {
        $this->pathStyle = rtrim($val, '\\/ ');
        ;
        return $this;
    }

    public function setPathCache(string $val)
    {
        $this->pathCache = rtrim($val, '\\/ ');
        ;
        return $this;
    }

    public function setUrl(string $val)
    {
        $this->url = $val;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function body($v = null)
    {
        if ($v === null) {
            return $this->body;
        }
        $v = $v.'.html';
        if (!file_exists($v)) {
            $v = $this->pathHtml.'/'.$v;
        }
        $this->body[] = $v;
        return $this;
    }

    public function header($v = null)
    {
        if ($v === null) {
            return $this->header;
        }
        if ($v === false) {
            $this->header = null;
        } else {
            $v = $v.'.html';
            if (!file_exists($v)) {
                $v = $this->pathHtml.'/'.$v;
            }
            $this->header = $v;
        }
        return $this;
    }

    public function footer($v = null)
    {
        if ($v === null) {
            return $this->footer;
        }
        if ($v === false) {
            $this->footer = null;
        } else {
            $v = $v.'.html';
            if (!file_exists($v)) {
                $v = $this->pathHtml.'/'.$v;
            }
            $this->footer = $v;
        }
        return $this;
    }




    public function render($html = null, $val = null)
    {
        if ($html !== null) {
            $this->body($html);
            $this->header(false);
            $this->footer(false);
        }
        if ($val !== null) {
            $this->val($val);
        }


        //Gerando o NAME da compilação para o cache.
        $this->cacheFile = $this->name.'_'
            .md5($this->request)
            .md5(
                implode('', $this->body)
                .implode('', $this->header)
                .implode('', $this->footer)
            );

        if (file_exists($this->pathCache.'/'.$this->cacheFile)) {
            return $this;
        }

        $this->content = file_exists($this->header) ? file_get_contents($this->header) : '';

        foreach ($this->body as $b) {
            $this->content .= file_get_contents($b);
        }
        $this->content .= file_exists($this->footer) ? file_get_contents($this->footer) : '';

        if ($this->mode == 'pro') {
            $this->setContent(str_replace(["\r","\n","\t",'  '], '', $this->getContent()));
        }

        //Html template processors
        if ($this->nTag) {
            $this->produceNTag();
        }
        if ($this->blade) {
            $this->blade();
        }

        //Insert cache data
        self::checkAndOrCreateDir($this->pathCache, true);

        //Delete this cache file at expiration
        $expiration = '<?php if(time() > '.(time() + $this->cacheTime).') unlink($this->pathCache.\'/\'.$this->cacheFile);?>';
        
        file_put_contents(
        
            $this->pathCache.'/'.$this->cacheFile,
                          $expiration.$this->getContent()
        
        );
        
        return $this;
    }

    /**
     * Style list insert
     */
    public function insertStyles($list)
    {
        if (!is_array($list)) {
            $list = [$list];
        }
        $this->styles = $list;
        return $this;
    }

    /**
     *  Javascript list insert
     */
    public function insertScripts($list)
    {
        if (!is_array($list)) {
            $list = [$list];
        }
        $this->scripts = $list;
        return $this;
    }


    /** SEND
     * Send headers & Output tris content
     */
    public function send()
    {
        if ($this->mode == 'pro') {
            ob_end_clean();
            ob_start('ob_gzhandler');
        }
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        header('Cache-Control: must_revalidate, public, max-age=31536000');
        header('X-Server: Qzumba/0.1.8.beta');//for safety ...
        header('X-Powered-By: NEOS PHP FRAMEWORK/1.3.0');//for safety ...

        if (file_exists($this->pathCache.'/'.$this->cacheFile)) {
            include $this->pathCache.'/'.$this->cacheFile;
            exit();
        } else {
            //$timer = £TIME.' - '.microtime(true).' = '.round((microtime(true)-£TIME)*1000, 2).'ms';
            exit(eval('?>'.$this->content));
        }
    }

    /**
     * Insere o conteúdo processado Html
     */
    protected function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Pega o conteúdo processado Html
     */
    protected function getContent()
    {
        return $this->content;
    }

    /**
     * Pega uma variável ou todas
     */
    public function getVar($var = null)
    {
        //return ($var == null) ? $this->values : (isset($this->values[$var]) ? $this->values[$var] : false);
        $var = trim($var);
        return ($var == null) ? static::$values : (isset(static::$values[$var]) ? static::$values[$var] : false);
    }


    /**
     * STATIC GET VAR
     */
    protected static function get($var = null)
    {
        return ($var == null) ? static::$values : (isset(static::$values[$var]) ? static::$values[$var] : false);
    }

    protected static function getScripts($var = null)
    {
        return ($var == null) ? static::$scripts : (isset(static::$scripts[$var]) ? static::$scripts[$var] : false);
    }


    /**
     * Registra uma variável para o Layout
     */
    public function value($name, $value = null)
    {
        return $this->val($name, $value);
    }
    public function val($name, $value = null)
    {
        if (is_string($name)) {
            static::$values[$name] = $value;
        }
        if (is_array($name)) {
            static::$values = array_merge(static::$values, $name);
        }
        return $this;
    }

    /**
     * Registra uma variável para o Javascript
     */
    public function jsvar($name, $value = null)
    {
        if (is_string($name)) {
            $this->jsvalues[$name] = $value;
        }
        if (is_array($name)) {
            $this->jsvalues = array_merge($this->jsvalues, $name);
        }
        return $this;
    }

    /**
     * Renderiza o arquivo html com ©NeosTags.
     *
     * @return void
    */
    private function produceNTag()
    {
        $ponteiro = -1;
        $content = $this->getContent();

        //Loop de varredura para o arquivo HTML
        while ($ret = $this->sTag($content, $ponteiro)) {
            $ponteiro = 0 + $ret['-final-'];
            $vartemp = '';

            //p($ret);

            //constant URL
            if ($ret['-tipo-'] == 'var' && $ret['var'] == 'url') {
                $vartemp = $this->url;
            } elseif (method_exists($this, '_' . $ret['-tipo-'])) {
                $vartemp = $this->{'_' . $ret['-tipo-']}($ret);
            } elseif (class_exists('\Lib\NTag\\NT'.ucfirst($ret['-tipo-']))) {
                $class = '\Lib\NTag\\NT'.ucfirst($ret['-tipo-']);
                $vartemp = (new $class)->make($ret);
            }

            //Incluindo o bloco gerado pelas ©NeosTags
            $content = substr_replace($this->getContent(), $vartemp, $ret['-inicio-'], $ret['-tamanho-']);
            $this->setContent($content);

            //RE-setando o ponteiro depois de adicionar os dados acima
            $ponteiro = $ret['-inicio-'];
        }//end while

        //returns the processed contents
        return $this->getContent();
    }

    /**
     * Scaner for ©NeosTag
     * Scans the file to find a ©NeosTag - returns an array with the data found ©NeosTag
     *
     * @param string $arquivo   file content
     * @param string $ponteiro  file pointer
     * @param string $tag       ©NeosTag to scan
     * @return array|false      array with the data found ©NeosTag or false (not ©NeosTag)
    */
    private function sTag(&$arquivo, $ponteiro = -1, $tag = null)
    {
        if ($tag == null) {
            $tag = $this->tag;
        }
        $inicio = strpos($arquivo, '<'.$tag, $ponteiro + 1);
        if ($inicio !== false) {
            //get the type (<s:tipo ... )
            $x = substr($arquivo, $inicio, 25);
            preg_match('/(?<tag>\w+):(?<type>\w+|[\:]\w+)/', $x, $m);
            if (!isset($m[0])) {
                return false;
            }

            $ntag = $m[0];
            //the final ...
            $ftag = strpos($arquivo, '</' . $ntag . '>', $inicio);
            $fnTag = strpos($arquivo, '/>', $inicio);
            $fn = strpos($arquivo, '>', $inicio);

            //not  /> or </s:xxx>  = error
            if ($fnTag === false && $ftag === false) {
                return false;
            }

            if ($ftag !== false) {
                if ($fn !== false && $fn < $ftag) {
                    $a['-content-'] = substr($arquivo, $fn+1, ($ftag - $fn)-1);
                    $finTag = $fn;
                    $a['-final-'] = $ftag + strlen('</'.$ntag.'>');
                } else {
                    return false;
                }
            } elseif ($fnTag !== false) {
                $a['-content-'] = '';
                $finTag = $fnTag;
                $a['-final-'] = $fnTag + 2;
            } else {
                return false;
            }

            //catching attributes
            preg_match_all('/(?<att>\w+)="(?<val>.*?)"/', substr($arquivo, $inicio, $finTag - $inicio), $atb);

            if (isset($atb['att'])) {
                foreach ($atb['att'] as $k => $v) {
                    $a[$v] = $atb['val'][$k];
                }
            }

            //block data
            $a['-inicio-'] = $inicio;
            $a['-tamanho-'] = ($a['-final-'] - $inicio);
            $a['-tipo-'] = 'var';

            if (strpos($m['type'], ':') !== false) {
                $a['-tipo-'] = str_replace(':', '', $m['type']);
            } else {
                $a['var'] = $m['type'];
            }

            return $a;
        }
        return false;
    }

    /**
     * Scaner para Blade.
     * Retorna o conteúdo substituindo variáveis BLADE (@var_name).
     *
     * @return void         O mesmo conteudo com variáveis BLADE substituídas
    */
    private function blade()
    {
        $arquivo = $this->getContent();

        $ret = preg_replace_callback('/@(.*?)[^\d\w\.\(\)\[\]]/', function ($cap) {
            $e = substr($cap[0], -1);
            $var = ($cap[1] == 'url') ? $this->url : '<?php echo \\'.__CLASS__.'::get("'.$cap[1].'")?>';
            return $var.($e != ' ' ? $e : '');
        }, $arquivo);

        return $this->setContent($ret);
    }


    /**
     * _var
     * Insert variable data assigned in view
     * Parameter "tag" is the tag type indicator (ex.: <s:variable  . . . tag="span" />)
     *
     * @param array $ret ©NeosTag data array
     * @return string   Renderized Html
    */
    private function _var($ret)
    {
        $v = $this->getVar($ret['var']);
        if (!$v) {
            return '';
        }
        $ret['-content-'] .= '<?php echo \\'.__CLASS__.'::get("'.trim($ret['var']).'")?>';

        return $this->setAttributes($ret);
    }


    /**
     * Generate and insert style sheets
     */
    private function _style($ret)
    {
        $s = '';
        foreach ($this->styles as $id => $f) {
            $tmp = strpos($f, 'http') !== false ? $f : $this->url.'/css/'.$f.'.css';
            $s .= '<link rel="stylesheet" href="'.$tmp.'" id="stylesheet_'.$id.'">'."\n\t";
        }
        $ret['-content-'] = $s;
        return $ret;
    }


    /**
     * Generate and insert scripts
     */
    private function _script($ret)
    {
        $s = '<script id="javascript_base">var _URL=\''.$this->url.'\'';

        foreach ($this->jsvalues as $n => $v) {
            $s .= ','.$n.'='.(is_string($v) ? '\''.str_replace("'", '"', $v).'\'' : $v);
        }
        $s .= ';</script>';
        
        foreach ($this->scripts as $id => $f) {
            $tmp = (strpos($f, 'http') !== false || strpos($f, '//') !== false) ? $f : $this->url.'/js/'.$f.'.js';
            $s .= "\n\t".'<script src="'.$tmp.'" id="javascript_'.$id.'"></script>';
        }

        $ret['-content-'] = $s;
        return $ret;
    }

    /**
     * Set attributes of html element
     *
     * @param array $a array of elements
     * @return strig contents
     */
    public function setAttributes($a)
    {
        $content = isset($a['-content-']) ? $a['-content-'] : '';
        $tag = isset($a['tag']) ? $a['tag'] : '';
        $a = $this->clearData($a);

        //Var span (with class, id, etc);
        if (count($a) > 0) {
            if ($tag == '') {
                $tag= 'span';
            }
            $d = '<'.$tag;
            foreach ($a as $k => $v) {
                $d .= ' '.trim($k).'="'.trim($v).'"';
            }

            if ($tag == 'input') {
                $content = $d.' value="'.$content.'"/>';
            } else {
                $content = $d.'>'.$content.'</'.$tag.'>';
            }
        } elseif ($tag != '') {
            if ($tag == 'input') {
                $content = '<'.$tag.' value="'.$content.'"/>';
            } else {
                $content = '<'.$tag.'>'.$content.'</'.$tag.'>';
            }
        }
        return $content;
    }


    /**
     * ClearData :: Clear all extra data.
     *
     * @param array $ret Starttag data array.
     * @return array Data array cleared.
    */
    public function clearData($ret)
    {
        unset(
            $ret['var'],
            $ret['-inicio-'],
            $ret['-tamanho-'],
            $ret['-final-'],
            $ret['-tipo-'],
            $ret['-content-'],
            $ret['tag'],
            $ret['data']
        );
        return $ret;
    }

    // Checa um diretório e cria se não existe - retorna false se não conseguir ou não existir
    /**
     * Check or create a directory
     * @param  string  $dir    path of the directory
     * @param  boolean $create False/true for create
     * @param  string  $perm   indiucates a permission - default 0777
     *
     * @return bool          status of directory (exists/created = false or true)
     */
    public static function checkAndOrCreateDir($dir, $create = false, $perm = 0777)
    {
        if (is_dir($dir) && is_writable($dir)) {
            return true;
        } elseif ($create === false) {
            return false;
        }

        @mkdir($dir, $perm, true);
        @chmod($dir, $perm);

        if (!is_writable($dir)) {
            return false;
        }
        
        return true;
    }


    /**
     * Shows page
     *
     * @param array $rqst
     * @param array $param
     * @param string $page full path of the page
     * @return void
     */
    public function show($rqst, $param, $page = 'body')
    {
        $this->render($page, ['request'=>$rqst,'params'=>$param])->send();
    }
}
