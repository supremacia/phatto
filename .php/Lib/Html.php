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
 * @link      Author contacts <http://billrocha.tk>
 */
namespace Lib;

/**
 * Html Class
 *
 * @category Html
 * @package  Library
 * @author   Bill Rocha <prbr@ymail.com>
 * @license  <https://opensource.org/licenses/MIT> MIT
 * @link     Author contacts <http://billrocha.tk>
 */
class Html
{

    private $name =             '';
    private $cached =           false;
    private $mode =             'dev'; //pro|dev

    private $pathHtml =         null;
    private $pathHtmlCache =    null;
    private $pathWww =          null;
    private $pathStyle =        null;
    private $pathScript =       null;
    private $url =              null;

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

    static private $values =    [];
    private static $node =      null;

    private $jsvalues =         [];
    private $block =            [];
    private $content =          '';
    private $tag =              'x:';


    /* Construct of Doc
     *
     *
     */
    public function __construct(
        $config = null,
        $name = null,
        $cached = null,
        $mode = null
    ) {
        if (is_array($config) && isset($config['pathHtml'])) {
            foreach ($config as $k => $v) {
                $this->{$k} = $v;
            }
        } elseif (method_exists('Config\Html', 'getParams')) {
            foreach ((new \Config\Html)->getParams() as $k => $v) {
                $this->{$k} = $v;
            }
        }

        if ($this->pathHtml === null) {
            $this->pathHtml = dirname(dirname(dirname(__DIR__))).'/Html';
        }

        if ($this->pathWww === null) {
            $this->pathWww  = dirname(dirname(dirname(dirname(__DIR__))));
        }

        if ($this->url === null) {
            $this->url = defined('_URL')  ? _URL  : './';
        }

        //Acertando o final com barras
        $this->url = rtrim($this->url, ' /');
        $this->pathHtml = rtrim($this->pathHtml, ' /');
        $this->pathWww = rtrim($this->pathWww, ' /');

        if ($this->pathHtmlCache === null) {
            $this->pathHtmlCache = $this->pathHtml.'cache';
        }

        if ($this->pathStyle === null) {
            $this->pathStyle = $this->pathWww.'css';
        }

        if ($this->pathScript === null) {
            $this->pathScript = $this->pathWww.'js';
        }

        if ($this->header === null) {
            $this->header = $this->pathHtml.'header.html';
        }

        if ($this->footer === null) {
            $this->footer = $this->pathHtml.'footer.html';
        }

        if ($name !== null) {
            $this->name =   $name;
        }
        if ($cached !== null) {
            $this->cached = $cached;
        }
        if ($mode !== null) {
            $this->mode =   $mode;
        }

        //Acertando o final com barras
        $this->pathHtmlCache = rtrim($this->pathHtmlCache, ' /');
        $this->pathScript = rtrim($this->pathScript, ' /');
        $this->pathStyle = rtrim($this->pathStyle, ' /');

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
        list($config, $name, $cached, $mode) = array_merge(func_get_args(), [null, null, null, null]);
        return static::$node = new static($config, $name, $cached, $mode);
    }

    //Html template processor: Blade
    public function setBlade(bool $blade = true)
    {
        $this->blade = $blade;
        return $this;
    }

    //Html template processor: NeosTag
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
        $this->pathHtmlCache = rtrim($val, '\\/ ');
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

    //Insert additional contents (ex.: data base), before produce
    public function insertBlock($tag, $contents)
    {
        $this->block[$tag] = $contents;
        return $this;
    }

    public function cached(bool $b = null)
    {
        $this->cached = $b;
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

        if ($this->cached && file_exists($this->pathHtmlCache.'/'.$this->name.'_cache.html')) {
            return $this;
        }

        $this->content = file_exists($this->header) ? file_get_contents($this->header) : '';

        foreach ($this->body as $b) {
            $this->content .= file_get_contents($b);
        }
        $this->content .= file_exists($this->footer) ? file_get_contents($this->footer) : '';

        if ($this->mode == 'dev') {
            $this->assets();
        }
        if ($this->mode == 'pro') {
            $this->assets();
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
        if ($this->cached) {
            self::checkAndOrCreateDir($this->pathHtmlCache, true);
            file_put_contents($this->pathHtmlCache.'/'.$this->name.'_cache.html', $this->getContent());
        }

        return $this;
    }

    /* Style list insert
    */
    public function insertStyles($list)
    {
        if (!is_array($list)) {
            $list = [$list];
        }
        $this->styles = $list;
        return $this;
    }

    /* Javascript list insert
    */
    public function insertScripts($list)
    {
        if (!is_array($list)) {
            $list = [$list];
        }
        $this->scripts = $list;
        return $this;
    }

    /* Produção dos links ou arquivos compactados.
     * para Style e Javascript
     *
     * Em modo 'dev' gera somente os links;
     * Em modo 'pro' compacta e obfusca os arquivos e insere diretamente no HTML.
     */
    private function assets()
    {
        $s = '';
        foreach ($this->styles as $id => $f) {
            $tmp = strpos($f, 'http') !== false ? $f : $this->url.'/css/'.$f.'.css';
            $s .= '<link id="stylesheet_'.$id.'" rel="stylesheet" href="'.$tmp.'">'."\n\t";
        }
        $this->val('style', $s);

        $s = '<script id="javascript_base">var _URL=\''.$this->url.'\'';

        foreach ($this->jsvalues as $n => $v) {
            $s .= ','.$n.'='.(is_string($v) ? '\''.str_replace("'", '"', $v).'\'' : $v);
        }
        $s .= ';</script>';
        
        foreach ($this->scripts as $id => $f) {
            $tmp = strpos($f, 'http') !== false ? $f : $this->url.'/js/'.$f.'.js';
            $s .= "\n\t".'<script id="javascript_'.$id.'" src="'.$tmp.'"></script>';
        }
        $this->val('script', $s); //e($this);
    }

    /* SEND
     * Send headers & Output tris content
     *
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

        if ($this->cached && file_exists($this->pathHtmlCache.'/'.$this->name.'_cache.html')) {
            return $this->sendWithCach();
        } else {
            //$timer = £TIME.' - '.microtime(true).' = '.round((microtime(true)-£TIME)*1000, 2).'ms';
            exit(eval('?>'.$this->content));
        }
    }

    /* Send cached version of compilation
     *
     */
    public function sendCache()
    {
        if (!file_exists($this->pathHtmlCache.'/'.$this->name.'_cache.html')) {
            $this->cached = true;
            return $this;
        }

        $this->setContent(file_get_contents($this->pathHtmlCache.'/'.$this->name.'_cache.html'));
        $this->send();
    }

    //Insere o conteúdo processado Html
    protected function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    //Pega o conteúdo processado Html
    protected function getContent()
    {
        return $this->content;
    }

    //Pega uma variável ou todas
    public function getVar($var = null)
    {
        //return ($var == null) ? $this->values : (isset($this->values[$var]) ? $this->values[$var] : false);
        $var = trim($var);
        return ($var == null) ? static::$values : (isset(static::$values[$var]) ? static::$values[$var] : false);
    }


    //STATIC GET VAR
    protected static function get($var = null)
    {
        return ($var == null) ? static::$values : (isset(static::$values[$var]) ? static::$values[$var] : false);
    }

    //Pega o conteúdo de um block
    public function getBlock($name = null)
    {
        return ($name == null) ? $this->block : (isset($this->block[$name]) ? $this->block[$name] : false);
    }

    //Registra uma variável para o Layout
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

    //Registra uma variável para o Javascript
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

        $ret = preg_replace_callback('/@(.*?)[^\d\w\.\(\)\[\]]/', function($cap){
            $e = substr($cap[0], -1);
            $var = ($cap[1] == 'url') ? $this->url : '<?php echo \Lib\Html::get("'.$cap[1].'")?>';
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
        //$ret['-content-'] .= $v;
        $ret['-content-'] .= '<?php echo \\'.__CLASS__.'::get("'.trim($ret['var']).'")?>';

        //List type
        if (is_array($v)) {
            return $this->_list($ret);
        }

        return $this->setAttributes($ret);
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
    static function checkAndOrCreateDir($dir, $create = false, $perm = 0777)
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
    function show($rqst, $param, $page = 'body')
    {
        $this->sendPage($page);
    }
}
