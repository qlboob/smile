<?php
Smile::requireOnce(SMILE_SYS_PATH.'function/writeFile.function.php');
class SmileTemplate {
	//config
	private $config		=	array();
	//template file
	private $template;
	//template variable
	private $tplVar		=	array();
	//cache id
	private $cacheId;
	//other parameters
	private $params;
	//stroe literal
	private $literal	=	array();
	//store html cahce
	private $htmlCache	=	array();
	//store no html cahce
	private $noHtmlCache	=	array();
	//the tag lib to compile
	private $tagLibs	=	array();
	//dependent function
	private $dependentFun		=	array(array());
	//repeat function
	private $repeatFun	=	array();
	//dependent widget
	private $dependentWidget	=	array(array());
	//repeat widget
	private $repeatWidget	=	array();
	//block
	private $block		=	array();
	//current compile times
	private $currentCompileTime	=	0;
	
	
	/**
	 * construct SmileTemplate
	 * @param Smile $smile
	 */
	function __construct() {
		$this->config	=	array(
			'cacheDir'		=>	Smile::config('cacheDir'),
			'tagDir'		=>	Smile::config('tagDir'),
			'tagBegin'		=>	$this->stripquote(Smile::config('tagBegin')),
			'tagEnd'		=>	$this->stripQuote(Smile::config('tagEnd')),
			'varBegin'		=>	$this->stripQuote(Smile::config('varBegin')),
			'varEnd'		=>	$this->stripQuote(Smile::config('varEnd')),
			'nested'		=>	Smile::config('nested'),
			'autoTags'		=>	Smile::config('autoTags'),
			'denyFun'		=>	Smile::config('denyFun'),
			'replace'		=>	Smile::config('replace'),
			'functionDir'	=>	Smile::config('functionDir'),
			'myFunctionDir'	=>	Smile::config('myFunctionDir'),
			'forceFuntion'	=>	Smile::config('forceFuntion'),
			'widgetDir'		=>	Smile::config('widgetDir'),
			'myWidgetDir'	=>	Smile::config('myWidgetDir'),
			'forceWidget'	=>	Smile::config('forceWidget'),
		
			'compressPhp'	=>	Smile::config('compressPhp'),
			'compressHtml'	=>	Smile::config('compressHtml'),
			'exeJs'			=>	Smile::config('exeJs'),
			'compressJs'	=>	Smile::config('compressJs'),
			'compressJsFile'	=>	Smile::config('compressJsFile'),
			'compressCssFile'	=>	Smile::config('compressCssFile'),
			'compressCss'	=>	Smile::config('compressCss'),
			'exeCss'		=>	Smile::config('exeCss'),
		
			'secureStr'		=>	Smile::config('secureStr'),
			'dependenceType'		=>	Smile::config('dependenceType'),
			'firstTags'		=>	Smile::config('firstTags'),
			'combinejs'		=>	Smile::config('combinejs'),
			'combinecss'	=>	Smile::config('combinecss'),
		);
	}
	
 	/**
 	 * protect/restore the exp string
 	 * @param string $str
 	 * @return string
 	 */
 	private function stripQuote($str,$back=FALSE) { 
 		$search		=	array('{','}','(',')','|','[',']');
 		$replace	=	array('\{','\}','\(','\)','\|','\[','\]');
 		if ($back) {
 			list($replace,$search)	=	array($search,$replace);
 		}
        $str = str_replace($search,$replace,$str);
        return $str;
    }
	
	function load($template,$tplVar,$cacheId=NULL,$params=NULL) {
		$this->tplVar	=	$tplVar;
		$this->template	=	$template;
		$this->cacheId	=	$cacheId;
		$this->params	=	$params;
		return $this->loadTemplate($template,$cacheId,$params);
	}
	
	function loadTemplate($template,$cacheId,$params) {
		$templateFilePath	=	Smile::getTemplateFilePath($template,$cacheId,$params);
		$cacheFilePath		=	Smile::getCacheFilePath($template,$cacheId,$params);
		$tplContent			=	file_get_contents($templateFilePath);
		
		//compile the template
		$compiledContent	=	$this->compile($tplContent);
		//html cache
		if (isset($params['htmlcache'])) {
			$compiledContent	=	$this->toHtml($compiledContent);
		}
		
		//restore block
		for ($i=0;FALSE !== stripos($compiledContent,'<!--###block ') && $i<3;++$i) {
			//$compiledContent	=	preg_replace('/<!--###block (\w*?) smile###-->/eis',"\$this->blockto('\\1')",$compiledContent);
			$compiledContent	=	preg_replace_callback('/<!--###block (\w*?) smile###-->/is',array(&$this,'blockto'),$compiledContent);
		}
		
		//restore no html cache
		while ($this->noHtmlCache)
		//$compiledContent = preg_replace('/<!--###nohtmlcache(\d+?)smile###-->/eis',"\$this->_reNoHtmlCache('\\1')",$compiledContent);
		$compiledContent = preg_replace_callback('/<!--###nohtmlcache(\d+?)smile###-->/is',array(&$this,'_reNoHtmlCache'),$compiledContent);
		
		//delete repeat function
		$this->delRepeatFun($compiledContent);
		//delete repeat widget
		$this->delRepeatWidget($compiledContent);
		
		//add secure string
		$compiledContent	=	$this->config['secureStr'].$compiledContent;
		
		//compress php & html code
		$compressKV			=	array(
			'html'		=>	'compressHtml',
			'php'		=>	'compressPhp',
			'js'		=>	'compressJs',
			'jsFile'	=>	'compressJsFile',
			'exeJs'		=>	'exeJs',
			'css'		=>	'compressCss',
			'exeCss'	=>	'exeCss',
			'cssFile'	=>	'compressCssFile',
			'combinecss'=>	'combinecss',
			'combinejs'	=>	'combinejs'
		);
		foreach ($compressKV as $key => $value) {
			$compress[$key]	=	isset($params[$value])?$params[$value]:$this->config[$value];
		}
		/*$compress['html']	=	isset($params['compressHtml'])?$params['compressHtml']:$this->config['compressHtml'];
		$compress['php']	=	isset($params['compressPhp'])?$params['compressPhp']:$this->config['compressPhp'];
		$compress['js']		=	isset($params['compressJs'])?$params['compressJs']:$this->config['compressJs'];
		$compress['jsFile']		=	isset($params['compressJsFile'])?$params['compressJsFile']:$this->config['compressJsFile'];
		$compress['css']	=	isset($params['compressCss'])?$params['compressCss']:$this->config['compressCss'];*/
		if ($compress) {
			$compressor	=	Smile::getInstance('CompressCode',TRUE);
			$compiledContent	=	$compressor->compress($compiledContent,$compress);
		}
		
		$this->addTplDepend($template);
		//write cache
		writeFile($cacheFilePath,$compiledContent);
		return $compiledContent;
	}
	
	/**
	 * compile template
	 * @param string $tplContent
	 * @return string
	 */
	function compile($tplContent) {
		//current compile time ++
		++$this->currentCompileTime;
		$begin	=	$this->config['tagBegin'];
		$end	=	$this->config['tagEnd'];
		//compile literal
//		$content	=	preg_replace("/{$begin}literal{$end}(.*?){$begin}\/literal{$end}/eis","\$this->_literal('\\1')",$tplContent);
		$content	=	preg_replace_callback("/{$begin}literal{$end}(.*?){$begin}\/literal{$end}/is",array(&$this,'_literal'),$tplContent);
		if ($this->config['replace']) {
			$content	=	str_replace(array_keys($this->config['replace']),array_values($this->config['replace']),$content);
		}
		
		//search tag library
		$this->_includeTag($content);
		if(isset($this->tagLibs[$this->currentCompileTime]))
		foreach ($this->tagLibs[$this->currentCompileTime] as $v) {
			//compile tag lib
			$this->_tagLib($v,$content,TRUE);
		}
		$autoTags	=	explode(',',$this->config['autoTags']);
		foreach ($autoTags as $v) {
			$this->_tagLib($v,$content);
		}
		
		$varBegin	=	$this->config['varBegin'];
		$varEnd		=	$this->config['varEnd'];
		//compile {$var} {@sss} on so on
//		$content	=	preg_replace("/$varBegin(\S.+?)$varEnd/eis","\$this->_other('\\1')",$content);
		//fixed by Luke 2010-07-23. if there is string "{{$sp}" , it matches all string
//		$content	=	preg_replace("/$varBegin([^\s{$varBegin}{$varEnd}]+?)$varEnd/eis","\$this->_other('\\1')",$content);
//		Luke 2010-07-24 except {\d|\s}
//		$content	=	preg_replace("/$varBegin(?!\s|\d)([^\s{$varBegin}{$varEnd}]+?)(?!\s|;)$varEnd/eis","\$this->_other('\\1')",$content);
		// fix by Luke 2010-08-23 , if there is space in {}. and don't match mutiple lines.
		//$content	=	preg_replace("/{$varBegin}([\\\$:~@#\.\^\*\/].+?)(?!\s|;)$varEnd/eis",
										//"\$this->_other('\\1')",$content);
		$content	=	preg_replace_callback("/{$varBegin}([\\\$:~@#\.\^\*\/].+?)(?!\s|;)$varEnd/is",
										array(&$this,'_other'),$content);

		//restore Literal<!--###htmlcache{$i}smile###-->
		//$content = preg_replace('/<!--###literal(\d+?)smile###-->/eis',"\$this->_reLiteral('\\1')",$content);
		$content = preg_replace_callback('/<!--###literal(\d+?)smile###-->/is',array(&$this,'_reLiteral'),$content);
		//restore html cache
		//$content = preg_replace('/<!--###htmlcache(\d+?)smile###-->/eis',"\$this->_reHtmlCache('\\1')",$content);
		$content = preg_replace_callback('/<!--###htmlcache(\d+?)smile###-->/is',array(&$this,'_reHtmlCache'),$content);
		
		
		//load depended function
		if (isset($this->dependentFun[$this->currentCompileTime])&&$this->dependentFun[$this->currentCompileTime]) {
			$functionStr	=	'';
			foreach ($this->dependentFun[$this->currentCompileTime] as $fun) {
				$functionStr	.=	$this->getDependentFunContent($fun);
			}
			//destory the dependent funtion
			unset($this->dependentFun[$this->currentCompileTime]);
			$content	=	$functionStr.$content;
		}
		//load depended widget
		if (isset($this->dependentWidget[$this->currentCompileTime]) && $this->dependentWidget[$this->currentCompileTime]) {
			$widgetStr	=	'';
			foreach ($this->dependentWidget[$this->currentCompileTime] as $widget){
				$widgetStr	.=	$this->getDependentWidgetContent($widget);
			}
			unset($this->dependentWidget[$this->currentCompileTime]);
			$content	=	$widgetStr.$content;
		}
		
		
		//current compile time --
		--$this->currentCompileTime;
		return $content;
	}
	
	/**
	 * parse the literal
	 * @param string $content
	 * @return string
	 */
	function _literal($matches) {
		$content	=	$matches[1];
//		stripPreg($content);
		static $i	=	0;
        $parseStr   =   "<!--###literal{$i}smile###-->";
        $this->literal[$i++]  = $content;
        return $parseStr;
	}
	
	/**
	 * restore literal
	 * @param integer $tag
	 */
	function _reLiteral($tag) {
		if ( is_array($tag) ) {
			$tag=$tag[1];
		}
        $parseStr   =  $this->literal[$tag];
        unset($this->literal[$tag]);
        return $parseStr;
	}
	
	
	/**
	 * search the tag to compile
	 * @param string $content
	 */
	function _includeTag(&$content) {
		$begin	=	$this->config['tagBegin'];
		$end	=	$this->config['tagEnd'];
		$find	=	preg_match("/{$begin}tagLib\s+?(.+?)\s*?\/?{$end}\s+/is",$content,$matches);
		if ($find) {
			$content = str_replace($matches[0],'',$content);
            $tagLibs = $matches[1];
            $tagLibs	=	explode(',',$tagLibs);
            array_map('trim',$tagLibs);
            array_filter($tagLibs,'strlen');
            $this->tagLibs[$this->currentCompileTime]	=	$tagLibs;
		}
		
	}
	
	/**
	 * compile tag library
	 * @param string $tag
	 * @param string $content
	 * @param boolean $hide
	 */
	function _tagLib($tag,&$content,$hide=FALSE) {
		//get taglib
		$className	=	'Tag'.ucwords($tag);
		$tagClass	=	Smile::getInstance($className.','.$this->config['tagDir'].$className.'.php',TRUE,$this);
		
		$begin	=	$this->config['tagBegin'];
		$end	=	$this->config['tagEnd'];
		$that=$this;
		foreach ($tagClass->getTag() as $key => $v) {
			isset($v['name'])	||	$v['name']=$key;
			if ($hide) {
				$sTag	=	"{$tag}:{$v['name']}";
			}else {
				$sTag	=	$v['name'];
			}
			$level	=	isset($v['nested'])?$this->config['nested']:1;
			if (FALSE !== strpos($content,$this->config['tagBegin'].$sTag)) {
				if (isset($v['nested']) || isset($v['content'])) { // there is some content
					for ($i=0;$i<$level;++$i){
						if (isset($v['attr'])) {
							//$content	=	preg_replace("|{$begin}{$sTag}\s+?(.*?)\s*?(?!/){$end}(.*?){$begin}/{$sTag}{$end}|eis",
															//"\$this->_tag(\$tagClass,'{$v['name']}','\\1','\\2')",
															//$content);
							$content	=	preg_replace_callback("|{$begin}{$sTag}\s+?(.*?)\s*?(?!/){$end}(.*?){$begin}/{$sTag}{$end}|is",
															function($matches) use ($that,$tagClass,$v){
																return $that->_tag($tagClass,$v['name'],$matches[1],$matches[2]);
															},
															$content);
						}else {
							//$content	=	preg_replace("|{$begin}{$sTag}{$end}(.*?){$begin}/{$sTag}{$end}|eis",
															//"\$this->_tag(\$tagClass,'{$v['name']}','','\\1')",
															//$content);
							$content	=	preg_replace_callback("|{$begin}{$sTag}{$end}(.*?){$begin}/{$sTag}{$end}|is",
															function($matches) use($that,$tagClass,$v){
																return $that->_tag($tagClass,$v['name'],'',$matches[1]);
															},
															$content);
						}
						
					}
				}else { //there is no content
					if (isset($v['attr'])) {
						//$content	=	preg_replace("|{$begin}{$sTag}\s+?(.*?)\s*?/{$end}|eis",
														//"\$this->_tag(\$tagClass,'{$v['name']}','\\1','')",
														//$content);
						$content	=	preg_replace_callback("|{$begin}{$sTag}\s+?(.*?)\s*?/{$end}|is",
														function($matches) use($that,$tagClass,$v){
															return $that->_tag($tagClass,$v['name'],$matches[1],'');
														},
														$content);
					}else {
						//$content	=	preg_replace("|{$begin}{$sTag}\s+?/{$end}|eis",
														//"\$this->_tag(\$tagClass,'{$v['name']}','\\1','')",
														//$content);
						$content	=	preg_replace_callback("|{$begin}{$sTag}\s+?/{$end}|is",
														function($matches) use ($that,$tagClass,$v){
															return $that->_tag($tagClass,$v['name'],$matches[1],'');
														},
														$content);
					}
				}
			}
			
		}
		
	}
	
	/**
	 * compile <tag:method />
	 * @param string $lib libary
	 * @param string $oneTag name of tag 
	 * @param string $attr this tag attribute
	 * @param string $content tag content
	 * @return string compiled content
	 */
	function _tag($lib,$oneTag,$attr,$content) {
		stripPreg($attr,$content);
		$method	=	"_$oneTag";
		return $lib->$method($attr,$content);
	}
	
	/**
	 * compile {$var}, $ is some special char
	 * @param string $tagStr
	 */
	function _other($tagStr){
		if ( is_array($tagStr) ) {
			$tagStr=$tagStr[1];
		}
		stripPreg($tagStr);
        //not compile the non-template tag
        if(preg_match('/^[\s|\d]/is',$tagStr))
            return $this->stripQuote($this->config['varBegin'],TRUE) . $tagStr .$this->stripQuote($this->config['varEnd'],TRUE);
        $flag =  substr($tagStr,0,1);
        $name   = substr($tagStr,1);
        switch ($flag) {
        	case '$':
	        	//compile {$varName}
	            return $this->_var($name);
        	case ':':
	        	$this->addDependFun($name);
	            // output function
	            return  '<?php echo '.$name.';?>';
        	case '~':
	        	$this->addDependFun($name);
	            // excute function
	            if (FALSE === strpos($name,'(')) {
	            	return '';
	            }
	            return  '<?php '.$name.';?>';
        	default:
        		$smileVar	=	array(
        			'@'	=>	'session',
        			'#'	=>	'cookie',
        			'.'	=>	'get',
        			'^'	=>	'post',
        			'*'	=>	'const',
        		);
        		if (isset($smileVar[$flag])) {
        			return $this->_var("Smile.".$smileVar[$flag].".$name");
        		}
        	break;
        }

        $stagStr = trim($tagStr);
        if(substr($stagStr,0,2)=='//' || (substr($stagStr,0,2)=='/*' && substr($stagStr,-2)=='*/'))
            //delete annotate
            return '';

        return $this->stripQuote($this->config['varBegin'],TRUE) .$tagStr .$this->stripQuote($this->config['varEnd'],TRUE);
	}
	
	/**
	 * compile variable({$name})
	 * @param string $varStr
	 */
	function _var($varStr){
        $varStr = trim($varStr);
        static $_varParseList = array();
        //cache
        if(isset($_varParseList[$varStr])) return $_varParseList[$varStr];
        $parseStr ='';
        if(!empty($varStr)){
            $varArray = explode('|',$varStr);
            //get the var name
            $var = array_shift($varArray);
            //TODO compile object property
           /* if(preg_match('/->/is',$var))
                return '';*/
            if('Smile.' == substr($var,0,6)){
                // comile smile var
                $name = $this->parseSmileVar($var);
            }
            elseif( false !== strpos($var,'.')) {
                //֧�� {$var.property}
                $vars = explode('.',$var);
                $var  =  array_shift($vars);
                switch(strtolower(Smile::config('TMPL_VAR_IDENTIFY'))) {
                    case 'array': //array
                        $name = '$'.$var;
                        foreach ($vars as $key=>$val)
                            $name .= '["'.$val.'"]';
                        break;
                    case 'obj':  // object
                        $name = '$'.$var;
                        foreach ($vars as $key=>$val)
                            $name .= '->'.$val;
                        break;
                    default:  // auto 
                        $name = 'is_array($'.$var.')?$'.$var.'["'.$vars[0].'"]:$'.$var.'->'.$vars[0];
                }
            }
            elseif(false !==strpos($var,':')){
                //compile {$var:property} 
                $vars = explode(':',$var);
                $var  =  str_replace(':','->',$var);
                $name = "$".$var;
                $var  = $vars[0];
            }
            elseif(false !== strpos($var,'[')) {
                //array {$var['key']} 
                $name = "$".$var;
                preg_match('/(.+?)\[(.+?)\]/is',$var,$match);
                $var = $match[1];
            }
            else {
                $name = "$$var";
            }
            //use fun
            if(count($varArray)>0)
                $name = $this->_varFunction($name,$varArray);
            $parseStr = '<?php echo '.$name.';?>';
        }
        $_varParseList[$varStr] = $parseStr;
        return $parseStr;
    }
    /**
     * compile function
     * @param $name variable name
     * @param $varArray
     * @return string 
     */
	function _varFunction($name,$varArray){
        //calculate function count
        $length = count($varArray);
        //exclude deny function
        $template_deny_funs = explode(',',$this->config['denyFun']);
        for($i=0;$i<$length ;$i++ ){
//            if (0===stripos($varArray[$i],'default='))
                $args = explode('=',$varArray[$i],2);
//            else
//                $args = explode('=',$varArray[$i]);
            //strip space
            $args[0] = trim($args[0]);
            //the default function is htmlspecialchars
            $args[0] || $args[0] = 'htmlspecialchars';
            switch(strtolower($args[0])) {
            case 'default':  // default function
                $name   = '('.$name.')?('.$name.'):'.$args[1];
                break;
            default:  // compile fun
                if(!in_array($args[0],$template_deny_funs)){
                	//add dependent fun
                   	$this->addDependFun($args[0]);
                    if(isset($args[1])){
                        if(strstr($args[1],'###')){
                            $args[1] = str_replace('###',$name,$args[1]);
                            $name = "$args[0]($args[1])";
                        }else{
                            $name = "$args[0]($name,$args[1])";
                        }
                    }else if(!empty($args[0])){
                        $name = "$args[0]($name)";
                    }
                }
            }
        }
        return $name;
    }
    /**
     * parse the smile variable
     * @param string $varStr
     * @return string
     */
	function parseSmileVar($varStr){
        $vars = explode('.',$varStr);
        array_shift($vars);
        $type = array_shift($vars);
        $type = strtoupper(trim($type));
        $parseStr = '';
        if(count($vars)>=1){
        	$smileA	=	"['".implode("']['",$vars)."']";
            switch($type){
                case 'SERVER':
                    $parseStr = '$_SERVER'.strtoupper($smileA);break;
                case 'GET':
                    $parseStr = '$_GET'.$smileA;break;
                case 'POST':
                    $parseStr = '$_POST'.$smileA;break;
                case 'COOKIE':
                    $parseStr = '$_COOKIE'.$smileA;break;
                case 'SESSION':
                    $parseStr = '$_SESSION'.$smileA;break;
                case 'ENV':
                    $parseStr = '$_ENV'.$smileA;break;
                case 'REQUEST':
                    $parseStr = '$_REQUEST'.$smileA;break;
                case 'CONST':
                    $parseStr = implode('.',$vars);break;
                /*case 'LANG':
                    $parseStr = 'L("'.$vars[2].'")';break;
				case 'CONFIG':
                    if(isset($vars[3])) {
                        $vars[2] .= '.'.$vars[3];
                    }
                    $parseStr = 'C("'.$vars[2].'")';break;*/
                default:break;
            }
        }
        return $parseStr;
    }
    /**
     * compile include tag
     * @param string $tmplPublicName
     * @return string
     */
	function _include($tmplPublicName){
		$this->addTplDepend($tmplPublicName);
        $tmplPublicName = trim($tmplPublicName);
        $tmplTemplateFile=Smile::getTemplateFilePath($tmplPublicName,$this->cacheId,$this->params);
        $parseStr = file_get_contents($tmplTemplateFile);
        //compile the content
        return $this->compile($parseStr);
    }
    
    /**
     * excute a string
     * @param $tpl file name or file path to execute
     * @param $content the content to execute
     */
    function _eval($tpl,$content) {
    	$this->exe($tpl,$content);
    }
    
    /**
     * assign tplvar
     * @param array $attr
     * @param string $content
     */
	function _assignTplVar($attr,$content) {
    	$value	=	$this->exe($attr['file'],$content);
    	$this->tplVar[$attr['name']]	=	$value;
    }
    
    /**
     * execute a file/string
     * @param string $tpl file name or path
     * @param string $content the execute string
     * @return excute result
     */
    function exe($tpl,$content='') {
    	if ($tpl) {
    		$tplFilePath	=	Smile::getTemplateFilePath($tpl,$this->cacheId,$this->params);
    		$content		=	file_get_contents($tplFilePath);
    		$this->addTplDepend($tplFilePath);
    	}
    	$____content	=	trim($content);
    	
    	//strip the php begin string and end string 
    	'<?php' == substr($____content,0,5) && $____content	=	substr($____content,6);
    	'?>' == substr($____content,-2) && $____content=substr($____content,0,-2);
    	$____content	=	trim($____content);
    	
    	//add the end ;
    	$lastChar	=	substr($____content,-1);
    	if (!in_array($lastChar,array('}',';'))) {
    		$____content	.=	';';
    	}
    	extract($this->tplVar);
    	return eval($____content);
    }
    
    /**
     * compile html cache
     * @param $tplName template file name
     * @param $tplContent compile content
     * @return string
     */
    function _htmlCache($tplName,$tplContent) {
    	static $i	=	0;
    	if ($tplName) {
	    	$content	=	$this->_include($tplName);
    	}else {
    		$content	=	$this->compile($tplContent);
    	}
    	
    	$cacheContent	=	$this->toHtml($content);
    	$str	=	"<!--###htmlcache{$i}smile###-->";
    	$this->htmlCache[$i++]	=	$cacheContent;
    	return $str;
    }
    
    /**
     * restore html cache
     * @param $tag string
     * @return string
     */
    function _reHtmlCache($tag) {
		if ( is_array($tag) ) {
			$tag=$tag[1];
		}
    	$parseStr   =  $this->htmlCache[$tag];
        unset($this->htmlCache[$tag]);
        return $parseStr;
    }
    
    /**
     * parse the no html cache
     * @param string $tplName
     * @param string $tplContent
     * @return string
     */
    function _noHtmlCache($tplName,$tplContent) {
    	static $i	=	0;
    	if ($tplName) {
	    	$cacheFilePath	=	Smile::getCacheFilePath($tplName,$this->cacheId,$this->params);
	    	$content	=	$this->_include($tplName);
    	}else {
    		$content	=	$this->compile($tplContent);
    	}
    	$str	=	"<!--###nohtmlcache{$i}smile###-->";
    	$this->noHtmlCache[$i++]	=	$content;
    	return $str;
    	
    }
    
	/**
	 * restore the no html cache
	 * @param integer $tag
	 * @return string:
	 */
	function _reNoHtmlCache($tag) {
		if ( is_array($tag) ) {
			$tag=$tag[1];
		}
    	$parseStr   =  $this->noHtmlCache[$tag];
        unset($this->noHtmlCache[$tag]);
        return $parseStr;
    }
    
    
    /**
     * add dependent function
     * @param string $name function name
     */
    function addDependFun($name) {
    	$exp		=	explode('(',$name);
    	$funName	=	trim($exp['0']);
    	if (!function_exists($funName) ||
    		in_array($funName,$this->dependentFun['0'])|| // if load the function in included file
    		in_array($funName,$this->config['forceFuntion'])){
    		if (!isset($this->dependentFun[$this->currentCompileTime]) ||
    			!in_array($funName,$this->dependentFun[$this->currentCompileTime]) ) {
	        	$this->dependentFun[$this->currentCompileTime][] = $funName;
	        	//add repeat function
	        	if ($this->dependentFun[0] && in_array($funName,$this->dependentFun['0'])) {
	        		$this->repeatFun[]	=	$funName;
	        	}
    		}
    		//add the all dependent functions
    		if (!$this->dependentFun['0'] || !in_array($funName,$this->dependentFun['0']) ) {
	        	$this->dependentFun['0'][] = $funName;
    		}
        }
    }
    
    function addDependWedget($name) {
    	if (!class_exists($name) ||
    		in_array($name, $this->dependentWidget[0])||
    		in_array($name, $this->config['forceWidget'])) {
    		if (!isset($this->dependentWidget[$this->currentCompileTime])||
    			!in_array($name, $this->dependentWidget[$this->currentCompileTime])) {
    				$this->dependentWidget[$this->currentCompileTime][]	=	$name;
    				//add repeat widget
    				if (in_array($name, $this->dependentWidget[0]))
    					$this->repeatWidget[]	=	$name;
    				
    				
    		}
    		//add the dependent widget
    		if (!in_array($name, $this->dependentWidget[0])) {
    			$this->dependentWidget[0][]	=	$name;
    		}
    	}
    }
    
    
    /**
     * add template dependent
     * @param $template template file
     */
    function addTplDepend($template) {
    	$template			=	trim($template);
    	$tmplTemplateFile	=	Smile::getTemplateFilePath($template,$this->cacheId,$this->params);
    	$deClsName			=	'TemplateDependent'.$this->config['dependenceType'];
    	$tplDependence		=	Smile::getInstance($deClsName.','.SMILE_PATH.'smilesys/TemplateDependent/'.$deClsName.'.php');
        $tplDependence->add($tmplTemplateFile,Smile::getCacheFilePath($this->template,$this->cacheId,$this->params));
    }
    
    /**
     * delete repeat function
     * @param $content
     */
    function delRepeatFun(&$content) {
    	foreach ($this->repeatFun as $fun) {
    		$funContent	=	$this->getDependentFunContent($fun);
    		if (empty($funContent)) {
    			continue;
    		}
    		$subCount	=	substr_count($content,$funContent);
    		if ($subCount>1) {
    			$pos	=	strpos($content,$funContent);
    			$len	=	strlen($funContent);
    			for ($i = 1; $i < $subCount; ++$i) {
    				if (($_pos=strpos($content,$funContent,$pos+$len)) !== FALSE) {
    					$content	=	substr_replace($content,'',$_pos,$len);
    				}
    			}
    		}
    	}
    	$this->repeatFun=array();
    }
    
    function delRepeatWidget(&$content) {
    	foreach ($this->repeatWidget as $widget){
    		$widgetContent	=	$this->getDependentWidgetContent($widget);
    		$subCount	=	substr_count($content,$widgetContent);
    		if ($subCount>1) {
    			$pos	=	strpos($content,$widgetContent);
    			$len	=	strlen($widgetContent);
    			for ($i = 1; $i < $subCount; ++$i) {
    				if (($_pos=strpos($content,$widgetContent,$pos+$len)) !== FALSE) {
    					$content	=	substr_replace($content,'',$_pos,$len);
    				}
    			}
    		}
    	}
    	$this->repeatWidget	=	array();
    }
    
    /**
     * convert template content and template variable to html
     * @param array $var
     * @param string $content
     * @return string
     */
    function toHtml($content) {
    	$tplCacheFile	=	tempnam($this->config['cacheDir'],'tmp');
    	writeFile($tplCacheFile,$this->config['secureStr'].$content);
    		ob_start();
    		ob_implicit_flush(FALSE);
    	extract($this->tplVar);
    	include $tplCacheFile;
    	$cacheContent	=	ob_get_clean();
    	unlink($tplCacheFile);
    	return $cacheContent;
    }
    
    /**
     * compile block
     * @param string $attr attributes
     * @param string $content block content
     */
    function block($attr,$content) {
    	if (isset($attr['file']) && !empty($attr['file'])) {
    		$cacheFilePath	=	Smile::getCacheFilePath($attr['file'],$this->cacheId,$this->params);
	    	$content	=	$this->_include($attr['file']);
    	}else {
    		$content	=	$this->compile($content);
    	}
    	$this->block[$attr['to']][/*$attr['name']*/]	=	array(
    		'content'	=>	$content,
//    		'to'		=>	$attr['to'],
    		'name'		=>	$attr['name'],
    		'before'	=>	isset($attr['before'])?$attr['before']:NULL,
    		'after'		=>	isset($attr['after'])?$attr['after']:NULL,
    	);
    	return '';
    }
    
    /**
     * compile blockto
     * @param string $name the name of locator
     * @return string
     */
    function blockto($name) {
		if ( is_array($name) ) {
			$name=$name[1];
		}
    	stripPreg($name);
    	$blocks		=	$this->block[$name];
    	if (!is_array($blocks)) {
    		return '';
    	}
    	
    	$tree		=	array();
    	$refer = array();
    	$beforeBlocks	=	array();
    	$afterBlocks	=	array();
    	
    	//to refer array
    	foreach ($blocks as $key => $value) {
    		$refer[$value['name']]	=&	$blocks[$key];
    	}
    	//array to tree
    	foreach ($blocks as $key => $value) {
    		if ('-'==$value['before']) {
    			$beforeBlocks[]	=&	$blocks[$key];
    		}elseif ('-'==$value['after']) {
    			$afterBlocks[]	=&	$blocks[$key];
    		}elseif ($value['before']){
    			$before	=&	$refer[$value['before']];
    			$before['_before'][]	=&	$blocks[$key];
    		}elseif ($value['after']){
    			$after	=&	$refer[$value['after']];
    			$after['_after'][]		=&	$blocks[$key];
    		}else {
    			$tree[]	=&	$blocks[$key];
    		}
    	}
    	$sortedBlock	=	array();
    	$sortedAfter	=	array();
    	$sortedBefore	=	array();
    	$beforeBlocks	=	array_reverse($beforeBlocks);
    	$this->treeToSortArray($tree,$sortedBlock);
    	$this->treeToSortArray($afterBlocks,$sortedAfter);
    	$this->treeToSortArray($beforeBlocks,$sortedBefore);
    	$ret	=	'';
    	foreach (array_merge($sortedBefore,$sortedBlock,$sortedAfter) as $value) {
    		$ret	.=	$value['content'];
    	}
    	return $ret;
    }
    
    /**
     * tree to array
     * @param array $tree tree array
     * @param array $arr sorted array
     * @return array 
     */
    private function treeToSortArray($tree, &$arr=array()) {
    	foreach ($tree as $value) {
	    	if (isset($value['_befroe']) && $value['_befroe']) {
	    		$this->treeToSortArray($value['_befroe'],$arr);
	    	}
	    	$arr[]	=	$value;
    		if (isset($value['_after'])&& $value['_after']) {
	    		$this->treeToSortArray($value['_after'],$arr);
	    	}
    	}
    }
    
    /**
     * get function file content
     * @param $funName function name
     * @return string
     */
    function getDependentFunContent($funName) {
    	//at first, search the user function directory
		$path	=	$this->config['myFunctionDir'].$funName.'.function.php';
		if (!file_exists($path)) {
			//is there the function file in the system function directory
	    	$path	=	$this->config['functionDir'].$funName.'.function.php';
			if (!file_exists($path)) {
				return '';
			}
		}
		return $this->getPhpFileContent ( $path );

    }
    
    function getDependentWidgetContent($name) {
    	$path	=	$this->config['myWidgetDir'].$name.'.php';
    	if (!file_exists($path)) {
    		$path	=	$this->config['widgetDir'].$name.'.php';
    		if (!file_exists($path))
    			return '';
    	}
    	return $this->getPhpFileContent($path);
    }
    
	/**
	 * Enter description here ...
	 * @param path
	 */
	private function getPhpFileContent($path) {
		$content	=	file_get_contents($path);
		$content	=	trim($content);
		substr($content,-2) != '?>' && $content.='?>';
		return $content;
	}

    
    /**
     * get template variable
     * @param string $key
     * @return multitype:
     */
    function get($key) {
    	return $this->tplVar[$key];
    }
}

/**
 * fix preg_replace function
 * @param string $str1
 * @param string $str2
 */
function stripPreg(&$str1,&$str2='') {
	$str1 && $str1	=	str_replace('\"', '"', $str1);
	$str2 && $str2	=	str_replace('\"','"',$str2);
}
