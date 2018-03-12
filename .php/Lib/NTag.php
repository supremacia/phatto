<?php

/**
 * Pedra\NTag
 * PHP version 7
 *
 * @category  Template
 * @package   Library
 * @author    Bill Rocha <prbr@ymail.com>
 * @copyright 2018 Bill Rocha <http://google.com/+BillRocha>
 * @license   <https://opensource.org/licenses/MIT> MIT
 * @version   GIT: 0.0.2
 * @link      Site <https://phatto.ga/ntag>
 */
namespace Lib;

/**
 * NTag Class
 *
 * @category Html
 * @package  Library
 * @author   Bill Rocha <prbr@ymail.com>
 * @license  <https://opensource.org/licenses/MIT> MIT
 * @link     Site <https://phatto.ga/ntag>
 */
class NTag
{
    private $name          = '';
    private $mode          = 'pro'; //pro|dev
    private $cacheTime     = 21600; // 6 hours of life
    private $cacheFile     = 'default.html';

    // Paths
    private $template      = null;
    private $cache         = null;
    private $style         = null;
    private $script        = null;
    private $url           = null;

    // Html parts
    private $header        = null;
    private $body          = [];
    private $footer        = null;

    //Plugins
    private $plugins       = [];

    // Assets
    private $styles        = [];
    private $scripts       = [];
    private $jsvar         = [];

    private static $var    = [];
    private static $node   = null;
    
    private $content       = '';


    /**
     * Construct of Doc
     */
    public function __construct(
        $name = null,
        $mode = null
    ) {
        // Nome da classe de configuração.
        $config = '\\Config\\'.__CLASS__;
        
        if (class_exists($config)) {
            // Instancia a classe, se necessário.
            new $config;

            // Carrega os parametros na classe atual.
            foreach ($this as $key => $val) {
                if (isset($config::$$key)) {
                    $this->$key = $config::$$key;
                }
            }
        }

        if ($name !== null) {
            $this->name =   $name;
        }

        if ($mode !== null) {
            $this->mode = $mode;
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

    /**
     * [setTemplate description]
     * @param [type] $val [description]
     */
    public function setTemplate($val)
    {
        $this->template = rtrim($val, '\\/ ');
        return $this;
    }

    /**
     * [setScript description]
     * @param string $val [description]
     */
    public function setScript(string $val)
    {
        $this->scripts[] = rtrim($val, '\\/ ');
        return $this;
    }

    /**
     * [setStyle description]
     * @param string $val [description]
     */
    public function setStyle(string $val)
    {
        $this->styles[] = rtrim($val, '\\/ ');
        return $this;
    }

    /**
     * [setCache description]
     * @param string $val [description]
     */
    public function setCache(string $val)
    {
        $this->cache = rtrim($val, '\\/ ');
        ;
        return $this;
    }

    /**
     * [setUrl description]
     * @param string $val [description]
     */
    public function setUrl(string $val)
    {
        $this->url = $val;
        return $this;
    }

    /**
     * [setName description]
     * @param [type] $name [description]
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }


    /**
     * add plugin class by name
     *
     * @param string $name  id name
     * @param string|object $class class/object or function to running
     */
    public function setPlugin($name, $class)
    {
        $this->plugins[$name] = $class;
        return $this;
    }

    /**
     * get a plugin by name
     *
     * @param string $name name id
     * @return string|objct an string of the class or a anonimal function
     */
    public function getPlugin($name)
    {
        return isset($this->plugins[$name]) ? $this->plugins[$name] : false;
    }

    /**
     * [insertStyles description]
     * @param  [type] $list [description]
     * @return [type]       [description]
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
     * [insertScripts description]
     * @param  [type] $list [description]
     * @return [type]       [description]
     */
    public function insertScripts($list)
    {
        if (!is_array($list)) {
            $list = [$list];
        }
        $this->scripts = $list;
        return $this;
    }

    /**
     * add/get html body path file
     *
     * @param  string $v filename of html file (without extension [.html])
     * @return array|object array of all body files or $this (in add)
     */
    public function body($v = null)
    {
        if ($v === null) {
            return $this->body;
        }
        $v = $v.'.html';
        if (!file_exists($v)) {
            $v = $this->template.'/'.$v;
        }
        $this->body[] = $v;
        return $this;
    }

    /**
     * [header description]
     * @param  [type] $v [description]
     * @return [type]    [description]
     */
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
                $v = $this->template.'/'.$v;
            }
            $this->header = $v;
        }
        return $this;
    }

    /**
     * [footer description]
     * @param  [type] $v [description]
     * @return [type]    [description]
     */
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
                $v = $this->template.'/'.$v;
            }
            $this->footer = $v;
        }
        return $this;
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

    /**
     * [render description]
     * @param  [type] $html [description]
     * @param  [type] $var  [description]
     * @return [type]       [description]
     */
    public function render($html = null, $var = null)
    {
        if ($html !== null) {
            $this->body($html);
        }
        if ($var !== null) {
            $this->var($var);
        }

        // Gerando o NAME da compilação para o cache.
        $this->cacheFile = $this->name.'_'.hash('sha256', implode('', $this->body).md5($this->header .$this->footer));

        // Rodando o cache
        if (file_exists($this->cache.'/'.$this->cacheFile)) {
            return $this;
        }

        // add header contents
        //$this->content = file_exists($this->header) ? file_get_contents($this->header) : '';
        
        // add all body contents
        foreach ($this->body as $b) {
            $this->content .= file_get_contents($b);
        }

        // add footer contents
        //$this->content .= file_exists($this->footer) ? file_get_contents($this->footer) : '';

        //Html template processors
        $this->produceNTag();
        $this->blade();

        // compressing
        if ($this->mode == 'pro') {
            $this->setContent($this->minifyHTML($this->getContent()));
        }

        // creates, if not exists, cache directory
        $this->checkAndOrCreateDir($this->cache, true);

        // delete this cache file at expiration command
        $expiration = '<?php if(time()>'.(time() + $this->cacheTime).') unlink($this->cache.\'/\'.$this->cacheFile);?>';
        
        // saving in cache
        file_put_contents($this->cache.'/'.$this->cacheFile, $expiration.$this->getContent());

        return $this;
    }

    /**
     * [send description]
     * @return [type] [description]
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

        if (file_exists($this->cache.'/'.$this->cacheFile)) {
            include $this->cache.'/'.$this->cacheFile;
            exit();
        } else {
            exit(eval('?>'.$this->content));
        }
    }

    /**
     * [setContent description]
     * @param [type] $content [description]
     */
    protected function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * [getContent description]
     * @return [type] [description]
     */
    protected function getContent()
    {
        return $this->content;
    }

    /**
     * [getVar description]
     * @param  [type] $var [description]
     * @return [type]      [description]
     */
    public function getVar($var = null)
    {
        $var = trim($var);
        return ($var == null) ? static::$var : (isset(static::$var[$var]) ? static::$var[$var] : false);
    }


    public function getData($data)
    {
        $content = $this->getVar($data['var']);
        if (!$content) {
            return '';
        }

        $data['-content-'] .= $content;
        return $this->setAttributes($data);
    }

    /**
     * [var description]
     * @param  [type] $name  [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function var($name, $value = null)
    {
        if (is_string($name)) {
            static::$var[$name] = $value;
        }
        if (is_array($name)) {
            static::$var = array_merge(static::$var, $name);
        }
        return $this;
    }

    /**
     * [jsvar description]
     * @param  [type] $name  [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function jsvar($name, $value = null)
    {
        if (is_string($name)) {
            $this->jsvar[$name] = $value;
        }
        if (is_array($name)) {
            $this->jsvar = array_merge($this->jsvar, $name);
        }
        return $this;
    }

    // ---------------------------------------------------------- privates -¬

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
            } elseif (class_exists(__CLASS__.'\\NT'.ucfirst(strtolower($ret['-tipo-'])))) {
                $class = __CLASS__.'\\NT'.ucfirst(strtolower($ret['-tipo-']));
                $vartemp = (new $class)->make($ret);
            }

            //Incluindo o bloco gerado pelas ©NeosTags
            $content = substr_replace($content, $vartemp, $ret['-inicio-'], $ret['-tamanho-']);

            //RE-setando o ponteiro depois de adicionar os dados acima
            $ponteiro = $ret['-inicio-'];
        }

        //returns the processed contents
        $this->setContent($content);
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
    private function sTag(&$arquivo, $ponteiro = -1, $tag = 'x:')
    {
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
            preg_match_all('/(?<att>\w+|\w+\-\w+)="(?<val>.*?)"/', substr($arquivo, $inicio, $finTag - $inicio), $atb);

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
            $var = ($cap[1] == 'url') ? $this->url : '<?php echo \\'.__CLASS__.'::this()->getVar("'.$cap[1].'")?>';
            return $var.($e != ' ' ? $e : '');
        }, $arquivo);

        return $this->setContent($ret);
    }


//------------------------------------------- Noes Tags Basic FUNCTIONS

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
        // instantiate and renderize
        $attributes = $this->clearData($ret);
        $attributes['-content-'] = $ret['-content-'];
        $attributes['var'] = $ret['var'];
        $att = '[';
        foreach ($attributes as $key => $val) {
            $val = str_replace("'", "\'", $val);
            $att .= "'$key'=>'$val',";
        }
        $att = substr($att, 0, -1).']';

        // add data call
        $ret['-content-'] = '<?php echo \\'.__CLASS__.'::this()->getData('.$att.')?>';

        return $ret;
    }


    /**
     * [_style description]
     * @param  [type] $ret [description]
     * @return [type]      [description]
     */
    private function _style($ret)
    {
        $s = '';
        foreach ($this->styles as $id => $f) {
            $tmp = strpos($f, 'http') !== false ? $f : $this->url.'/css/'.$f;
            $s .= '<link rel="stylesheet" href="'.$tmp.'" id="stylesheet_'.$id.'">'."\n\t";
        }
        $ret['-content-'] = $s;
        return $ret;
    }

    /**
     * [_script description]
     * @param  [type] $ret [description]
     * @return [type]      [description]
     */
    private function _script($ret)
    {
        $s = '<script id="javascript_base">var _URL=\''.$this->url.'\'';

        foreach ($this->jsvar as $n => $v) {
            $s .= ','.$n.'='.(is_string($v) ? '\''.str_replace("'", '"', $v).'\'' : $v);
        }
        $s .= ';</script>';
        
        foreach ($this->scripts as $id => $f) {
            $tmp = (strpos($f, 'http') !== false || strpos($f, '//') !== false) ? $f : $this->url.'/js/'.$f;
            $s .= "\n\t".'<script src="'.$tmp.'" id="javascript_'.$id.'"></script>';
        }

        $ret['-content-'] = $s;
        return $ret;
    }

    /**
     * [_part description]
     * @param  [type] $ret [description]
     * @return [type]      [description]
     */
    private function _part($ret)
    {
        if (!isset($ret['file']) || !file_exists($this->template.'/'.$ret['file'].'.html')) {
            return '';
        }

        $ret['-content-'] = file_get_contents($this->template.'/'.$ret['file'].'.html');
        return $ret;
    }

    /**
     * [_header description]
     * @param  [type] $ret [description]
     * @return [type]      [description]
     */
    private function _header($ret)
    {
        if (!file_exists($this->header)) {
            return '';
        }
         
        $ret['-content-'] = file_get_contents($this->header);
        return $ret;
    }

    /**
     * [_footer description]
     * @param  [type] $ret [description]
     * @return [type]      [description]
     */
    private function _footer($ret)
    {
        if (!file_exists($this->footer)) {
            return '';
        }

        $ret['-content-'] = file_get_contents($this->footer);
        return $ret;
    }


    // -------------------------------------- OTHERS -----------------

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
            $ret['data'],
            $ret['load']
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
    public function checkAndOrCreateDir($dir, $create = false, $perm = 0777)
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
     * Minify HTML string
     *
     * @param string $html
     * @return string
     */
    private function minifyHTML($html)
    {
        $pattern = '/<(?<script>script).*?<\/script\s*>|<(?<style>style).*?<\/style\s*>|<!(?<comment>--).*?-->|<(?<tag>[\/\w.:-]*)(?:".*?"|\'.*?\'|[^\'">]+)*>|(?<text>((<[^!\/\w.:-])?[^<]*)+)|/si';                
        preg_match_all($pattern, $html, $matches, PREG_SET_ORDER);
        $overriding = false;
        $raw_tag = false;
        // Variable reused for output
        $html = '';
        foreach ( $matches as $token ) {
            $tag = (isset($token['tag'])) ? strtolower($token['tag']) : null;    
            $content = $token[0];
                            
            if ( is_null( $tag ) ) {                            
                if ( !empty( $token['script'] ) ) {                                    
                    $strip = true;                                        
                } else if ( !empty($token['style'] ) ) {                                    
                    $strip = true;                                                                               
                } else if ( $this->remove_comments ) {                                    
                    if ( !$overriding && $raw_tag != 'textarea' ) {                                            
                        // Remove any HTML comments, except MSIE conditional comments
                        $content = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $content);                                                
                    }
                }                                
            } else {                            
                if ( $tag == 'pre' || $tag == 'textarea' || $tag == 'script' ) {                                    
                    $raw_tag = $tag;                                        
                } else if ( $tag == '/pre' || $tag == '/textarea' || $tag == '/script' ) {                                    
                    $raw_tag = false;                                        
                } else {                                        
                    if ($raw_tag || $overriding) {                                            
                        $strip = false;                                                
                    } else {                                            
                        $strip = true;                                                
                        // Remove any empty attributes, except:
                        // action, alt, content, src
                        $content = preg_replace('/(\s+)(\w++(?<!\baction|\balt|\bcontent|\bsrc)="")/', '$1', $content);
                                                    
                        // Remove any space before the end of self-closing XHTML tags
                        // JavaScript excluded
                        $content = str_replace(' />', '/>', $content);                                                
                    }
                }
            }
            if ( $strip ) {                            
                $content = str_replace(["\r","\n","\t",'  '], '', $content);                             
            }
            $html .= $content;                        
        }                
    return $html;
    }
}
