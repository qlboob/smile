<?php

class CompressCode {
	
	private $config;
	
	private $jsFiles	=	array();
	
	private $cssFiles	=	array();
	
//	private $tagSmile;
	
	private $minifyURL;
	
	private $minifGroup	=	NULL;
	
	private $minifyGroupsConfigFilePath;
	
	private $writeMinifConfig	=	FALSE;
	
	function __construct() {
		$this->config	=	array(
			'jsDir'		=>	Smile::config('jsDir'),
			'cssDir'	=>	Smile::config('cssDir'),
		);
		$this->minifyURL					=	Smile::config('minifyURL');
		$this->minifyGroupsConfigFilePath	=	Smile::config('minifyGroupsConfigFilePath');
		$minifyGroup						=	include $this->minifyGroupsConfigFilePath;
		is_array($minifyGroup) || $minifyGroup = array();
		$this->minifGroup					=	$minifyGroup;
	}

	/**
	 * compress code
	 * @param $mixCode mix code with html and php code
	 * @param $compress compress parameters
	 * @return compressed code
	 */
	function compress($mixCode,$compress) {
		//compress?
		$flag	=	FALSE;
		foreach ($compress as $value) {
			if($value){
				$flag	=	TRUE;
				break;
			}
		}
		if ($flag) {
			$ret	=	'';
			$block	=	$this->extractPhpAndHtml($mixCode);
			$compiler	=	array(
				'js'	=>	array(
					'find'=>'</script>', 
					'pattern'=>'#<script(.*?)>(.*?)</script>#eis',
					'replace'=>"\$this->trimJs('\\1','\\2')"
				),
				'exeJs'	=>	array(
					'find'=>'</script>', 
					'pattern'=>'#<script(.*?)></script>#eis',
					'replace'=>"\$this->exeJsFile('\\1')"
				),
				'jsFile'=>	array(
					'find'=>'</script>', 
					'pattern'=>'#<script(.*?)></script>#eis',
					'replace'=>"\$this->trimJsFile('\\1')"
				),
				'css'	=>	array(
					'find'=>'</style>', 
					'pattern'=>'#<style(.*?)>(.*?)</style>#eis',
					'replace'=>"\$this->trimCss('\\1','\\2')"
				),
				'exeCss'=>	array(
					'find'=>'<link ', 
					'pattern'=>'#<link(.*?)/>#eis',
					'replace'=>"\$this->exeCssFile('\\1')"
				),
				'cssFile'=>	array(
					'find'=>'<link ', 
					'pattern'=>'#<link(.*?)/>#eis',
					'replace'=>"\$this->trimCssFile('\\1')"
				),
				'combinecss'=>	array(
					'find'=>'<link ', 
					'pattern'=>'#<link(.*?)/>#eis',
					'replace'=>"\$this->combineCss('\\1')"
				),
				'combinejs'=>	array(
					'find'=>'</script>', 
					'pattern'=>'#<script(.*?)></script>#eis',
					'replace'=>"\$this->combineJs('\\1')"
				),
			);
			for ($i = 0,$_count=count($block); $i < $_count; ++$i) {
				if ('html' == $block[$i]['0']) {
					$htmlCode	=	$block[$i]['1'];
					
					if (isset($compress['html']) && $compress['html']) {
						$htmlCode	=	$this->trimHtml($htmlCode);
					}
					/*if ($compress['js'] && FALSE !== stripos($htmlCode,'</script>')) {
						$htmlCode	=	preg_replace('#<script(.*?)>(.*?)</script>#eis',
														"\$this->trimJs('\\1','\\2')",$htmlCode);
					}
					
					if ($compress['exeJs']) {
						$htmlCode	=	preg_replace('/<script(.*?)><\/script>/eis',
														"\$this->exeJsFile('\\1')",$htmlCode);
					}
					if ($compress['jsFile'] && FALSE !== stripos($htmlCode,'</script>')) {
						$htmlCode	=	preg_replace('/<script(.*?)><\/script>/eis',
														"\$this->trimJsFile('\\1')",$htmlCode);
					}
					
					if ($compress['css'] && FALSE !== stripos($htmlCode, '</style>')) {
						$htmlCode	=	preg_replace('#<style(.*?)>(.*?)</style>#eis',
														"\$this->trimCss('\\1','\\2')",$htmlCode);
					}
					if ($compress['exeCss'] && FALSE !== stripos($htmlCode, '<link ')) {
						$htmlCode	=	preg_replace('#<link(.*?)/>#eis',
														"\$this->exeCssFile('\\1')",$htmlCode);
					}
					if ($compress['cssFile'] && FALSE !== stripos($htmlCode, '<link ')) {
						$htmlCode	=	preg_replace('#<link(.*?)/>#eis',
														"\$this->trimCssFile('\\1')",$htmlCode);
					}*/
					foreach ($compiler as $key => $value) {
						if (isset($compress[$key]) && $compress[$key] && FALSE !==stripos($htmlCode, $value['find'])) {
							$htmlCode	=	preg_replace($value['pattern'],$value['replace'],$htmlCode);
						}
					}
					/*if (isset($compress['combine']) && $compress['combine']) {
						if (FALSE !== strpos($htmlCode,'<link ')) {
							$htmlCode	=	preg_replace('#<link(.*?)/>#eis',
														"\$this->combineCss('\\1')",$htmlCode);
						}
						if (FALSE !== stripos($htmlCode,'</script>')) {
							$htmlCode	=	preg_replace('/<script(.*?)><\/script>/eis',
														"\$this->combineJs('\\1')",$htmlCode);
						}
					}*/
					
					if (trim($htmlCode) || FALSE !== strpos($htmlCode,' ')) {
						$ret	.=	$htmlCode;
					}
				}else {
					if ($compress['php']) {
						$ret.=$this->trimPhp($block[$i]['1']);
					}else {
						$ret.=$block[$i]['1'];
					}
				}
			}
			//restore combine
			/*if (isset($compress['combine']) && $compress['combine']) {
//				$this->reCombine($ret);
				if (FALSE !== stripos($ret,'<!--###css ')) {
					$ret	=	preg_replace('/<!--###css (\d+) #smile-->/eis',"\$this->reCombine('\\1','css')",$ret);
				}
				if (FALSE !== stripos($ret,'<!--###js ')) {
					$ret	=	preg_replace('/<!--###js (\d+) #smile-->/eis',"\$this->reCombine('\\1','js')",$ret);
				}
			}*/
			if (isset($compress['combinejs']) && $compress['combinejs'] && FALSE !== stripos($ret,'<!--###js ')) {
				$ret	=	preg_replace('/<!--###js (\d+) #smile-->/eis',"\$this->reCombine('\\1','js')",$ret);
			}
			if (isset($compress['combinecss']) && $compress['combinecss'] && FALSE !== stripos($ret,'<!--###css ')) {
				$ret	=	preg_replace('/<!--###css (\d+) #smile-->/eis',"\$this->reCombine('\\1','css')",$ret);
			}
			if ($this->writeMinifConfig) {
				file_put_contents($this->minifyGroupsConfigFilePath,'<?php return '.var_export($this->minifGroup,TRUE).';');
			}
			return $ret;
		}else {
			return $mixCode;
		}
	}
	
	/**
	 * get splited PHP and Html code
	 * @param string $content mix code with php and html
	 * @return array 
	 */
	function extractPhpAndHtml($content) {
		$lines					=	preg_split("/[\r\n][\r\n]?/",$content,-1, PREG_SPLIT_NO_EMPTY);
		$bloc					=	array();
		$php_bloc_number			=	0;
		$multiline_php_code		=	'';
		$multiline_html_code	=	'';
		$double_increment		=	FALSE;
		$in_php_code			=	FALSE;
		$in_single_quoted		=	FALSE;
		$in_double_quoted		=	FALSE;
		$in_comment_multi_line	=	FALSE;
		$in_comment_single_line	=	FALSE;
		foreach ($lines as $line_mixed_code) {
			$line_mixed_code.="\n";
			$nb_char	=	strlen($line_mixed_code);
			$i_inline	=	0;
			while ($i_inline<$nb_char){
				if ($in_php_code) {
					$multiline_php_code	.=	$line_mixed_code[$i_inline];
					if($double_increment){
						$i_inline++;
						$multiline_php_code.=$line_mixed_code[$i_inline];
						$double_increment=false;
					}
					// specification in http://fr.php.net/manual/fr/language.types.string.php
					if($in_comment_multi_line)
					{// search ending sequence
						if (($line_mixed_code [$i_inline] == '*') && (($i_inline + 1) < $nb_char) && ($line_mixed_code [$i_inline + 1] == '/')) {
							$in_comment_multi_line = false;
							$double_increment = true;
						}
					}
					elseif($in_single_quoted)
					{// search ending sequence 
						if (($line_mixed_code[$i_inline]=='\\')&&(($i_inline+1)<$nb_char)&&(
						($line_mixed_code[($i_inline+1)]=='\'')||($line_mixed_code[$i_inline+1]=='\\')
						))$double_increment=true;
						elseif ($line_mixed_code[$i_inline]=='\'') $in_single_quoted=false;
					}
					elseif($in_double_quoted)
					{// search ending sequence
						if (($line_mixed_code[$i_inline]=='\\')&&(($i_inline+1)<$nb_char)&&(
						($line_mixed_code[($i_inline+1)]=='\'')||($line_mixed_code[$i_inline+1]=='\\')
						||($line_mixed_code[$i_inline+1]=='n')||($line_mixed_code[$i_inline+1]=='r')
						||($line_mixed_code[$i_inline+1]=='t')||($line_mixed_code[$i_inline+1]=='v')
						||($line_mixed_code[$i_inline+1]=='f')||($line_mixed_code[$i_inline+1]=='$')
						||($line_mixed_code[$i_inline+1]=='"')
						))$double_increment=true;
						elseif ($line_mixed_code[$i_inline]=='"') $in_double_quoted=false;					
					}
					elseif ((($i_inline+1)<$nb_char)&&($line_mixed_code[$i_inline]=='?')&&($line_mixed_code[($i_inline+1)]=='>')) {
						if ($php_bloc_number>0 && $bloc[$php_bloc_number-1][0]=='php') {
							/*strip last string '?>'*/
							$lastPhpCode	=	substr($bloc[$php_bloc_number-1][1],0,-2);
							//trim
							$lastPhpCode	=	trim($lastPhpCode);
							$lastChar		=	substr($lastPhpCode,-1);
							if (!in_array($lastChar, array(';','}','{',':'))) {
								//add last ;
								$lastPhpCode.=';';
							}
							$currentPhpCode	=	substr($multiline_php_code.'>',5);
							$bloc[$php_bloc_number-1][1]	=	$lastPhpCode.trim($currentPhpCode);
						}else {
							$bloc[$php_bloc_number][0] = 'php';
							$bloc[$php_bloc_number][1] = '<'.$multiline_php_code.'>';
							$php_bloc_number++;
						}
						// reset in_php_code values
						$multiline_php_code="";
						$in_php_code=false;
						$in_comment_single_line=false;
						$double_increment=true;
						$i_inline++;
					}
					elseif((!$in_comment_multi_line)&&!$in_comment_single_line)
					{// not in comment
						if ($line_mixed_code[$i_inline]=='\'') $in_single_quoted=true;
						elseif ($line_mixed_code[$i_inline]=='"') $in_double_quoted=true;
						elseif ($line_mixed_code[$i_inline]=='#') $in_comment_single_line=true;
						elseif (($i_inline+1)<$nb_char)
						{// next char exist
							if (($line_mixed_code[$i_inline]=='/')&&($line_mixed_code[($i_inline+1)]=='*')) {$in_comment_multi_line=true;;$double_increment=true;}
							elseif (($line_mixed_code[$i_inline]=='/')&&($line_mixed_code[($i_inline+1)]=='/')) {$in_comment_single_line=true;;$double_increment=true;}
						}// end next char exist
					}//end :  not in comment
				}else {
					// searching php open tag : with strpos
					$substr			=	substr($line_mixed_code,$i_inline);
					$pos_open_tag	=	strpos($substr ,'<?php');
					if (FALSE === $pos_open_tag) {
	//					$i_inline				=	$nb_char+1;
						$multiline_html_code	.=	$substr;
						break;
					}else {
						$htmlContent				=	$multiline_html_code.substr($substr,0,$pos_open_tag);
						//if there is a blackspace, do not delete
						if(trim($htmlContent) || strpos($htmlContent,' ')!==FALSE) 
							$bloc[$php_bloc_number++]	=	array('html',$htmlContent);
						//reset html code
						$multiline_html_code	=	'';
						//clear i_inline
						$i_inline				+=	$pos_open_tag;
	//					$bloc[$bloc_number]=array("start_line" => $line_number,"start_pos" => $i_inline,"end_line" => null,"end_pos" => null,"code" => null);
						$in_php_code=true;
					}
				}// end  searching php open tag
				++$i_inline;
			}
			if(!$in_php_code && $multiline_html_code){
				$bloc[$php_bloc_number]	=	array('html',$multiline_html_code);
			}
	//		echo $line_mixed_code;
		}
		return $bloc;
	}
	
	/**
	 * trim php code
	 * @param string $content
	 * @return string
	 */
	function trimPhp($content) {
	    $stripStr = '';
	    //analyse code
	    $tokens =   token_get_all ($content);
	    $last_space = false;
	    for ($i = 0, $j = count ($tokens); $i < $j; $i++)
	    {
	        if (is_string ($tokens[$i]))
	        {
	            $last_space = false;
	            $stripStr .= $tokens[$i];
	        }
	        else
	        {
	            switch ($tokens[$i][0])
	            {
	                //delete annotation
	                case T_COMMENT:
	                case T_DOC_COMMENT:
	                    break;
	                //delete space
	                case T_WHITESPACE:
	                    if (!$last_space)
	                    {
	                        $stripStr .= ' ';
	                        $last_space = true;
	                    }
	                    break;
	                default:
	                    $last_space = false;
	                    $stripStr .= $tokens[$i][1];
	            }
	        }
	    }
	    return $stripStr;
	}
	/**
	 * trim html code
	 * @param string $source
	 * @return string
	 */
	function trimHtml($source) {
		$protectTags	=	array(
			'SCRIPT'	=>	"!<script[^>]*?>.*?</script>!is",
			'PRE'		=>	"!<pre[^>]*?>.*?</pre>!is",
			'TEXTAREA'	=>	"!<textarea[^>]*?>.*?</textarea>!is",
		);
		foreach ($protectTags as $key => $value) {
			preg_match_all($value, $source, $match);
		    $$key = $match[0];
		    $source = preg_replace($value,"<SMILE:TRIM:$key>", $source);
		}
		
		$source	=	preg_replace("~>\s+<~", '><', $source);
		
		foreach ($protectTags as $key => $value) {
			$this->trimhtmlwhitespace_replace("<SMILE:TRIM:$key>",$$key, $source);
		}
		return $source;
	}
	
	/**
	 * restore the proected string tag
	 * @param string $search_str 
	 * @param string $replace
	 * @param string $subject
	 */
	function trimhtmlwhitespace_replace($search_str, $replace, &$subject) {
	    $_len = strlen($search_str);
	    $_pos = 0;
	    for ($_i=0, $_count=count($replace); $_i<$_count; $_i++)
	        if (($_pos=strpos($subject, $search_str, $_pos))!==false)
	            $subject = substr_replace($subject, $replace[$_i], $_pos, $_len);
	        else
	            break;
	
	}
	
	/**
	 * compress the script tag js content
	 * @param string $attr
	 * @param string $content
	 * @return string
	 */
	function trimJs($attr,$content) {
		stripPreg($attr,$content);
		if ($content){
			$content	=	$this->trimJsContent($content);
		}
		
		return "<script{$attr}>$content</script>";
	}
	
	function trimJsContent($content) {
		Smile::requireOnce(SMILE_PATH.'vendor/JavaScriptPacker.php');
		$packer = new JavaScriptPacker($content, 0, FALSE, FALSE);
		$content	=	$packer->pack();
		return $content;
	}
	
	function trimCss($attr, $content) {
		stripPreg($attr,$content);
		
		return "<style{$attr}>".$this->trimCssContent($content).'</style>';
			
	}
	private function trimCssContent($content) {
		$css	=	Smile::getInstance('csstidy'.','.SMILE_PATH.'vendor/csstidy/class.csstidy.php');
		$css->set_cfg('remove_last_;',true);
		$css->set_cfg('case_properties',1);
		$css->set_cfg('merge_selectors', 1);
		$css->set_cfg('optimise_shorthands',1);
		$css->set_cfg('merge_selectors', 1);
		$css->set_cfg('optimise_shorthands',1);
		$css->set_cfg('css_level','CSS2.1');
			
			
		$css->load_template('highest_compression');
		$css->parse($content);
		
		return $css->print->plain();
	}
		
	function trimJsFile($attr) {
		$attr	=	$this->trimFile($attr,'src',$this->config['jsDir'],'trimJsContent');
		return "<script{$attr}></script>";
		
	}
	
	private function trimFile($attr,$src,$dir,$trimFnc) {
		stripPreg($attr);
		
		$tags		=	BaseTag::getAttrArray($attr);
		$attrSrc	=	$tags[$src];
		$notrim		=	BaseTag::getAttr('nocompress',$tags);
		if (!$notrim && !strpos($attrSrc,'?') && !strpos($attrSrc,'&') && !strpos($attrSrc,'=') ) {
			$oldPath	=	$this->getExternalFilePath($attrSrc,$dir);
			if ($oldPath) {
				$newPath	=	$this->getNewExternalFilePath($oldPath,'s.');
				if (!file_exists($newPath) || filemtime($oldPath)>filemtime($newPath)) {
					$content	=	file_get_contents($oldPath);
					$content	=	$this->$trimFnc($content);
					writeFile($newPath,$content);
				}
				$tags[$src]	=	$this->getNewExternalFilePath($attrSrc,'s.');
			}
		}
		return BaseTag::buildAttr($tags);
	}
	
	function trimCssFile($attr) {
		$attr	=	$this->trimFile($attr,'href',$this->config['cssDir'],'trimCssContent');
		return "<link{$attr} />";
	}
	
	function exeJsFile($attr) {
		$attr	=	$this->exeFile($attr,'src',$this->config['jsDir']);
		
		return "<script{$attr}></script>";
	}
	
	function exeCssFile($attr) {
		$attr	=	$this->exeFile($attr,'href',$this->config['cssDir']);
		
		return "<link{$attr} />";
	}
	
	function combineCss($attr) {
		return $this->combine($attr,'css');
	}
	
	function combineJs($attr) {
		return $this->combine($attr,'js');
	}
	
	function combine($attr,$type) {
		static $_css	=	0;
		static $_js		=	0;
		stripPreg($attr);
		$srcAttr	=	array('js'=>'src','css'=>'href');
		$srcKey		=	$srcAttr[$type];
		$tags		=	BaseTag::getAttrArray($attr);
		
		$no			=	BaseTag::getAttr('nocombine',$tags);
		
		$src		=	$tags[$srcKey];
		$oFile		=	$this->getExternalFilePath($src,$this->config[$type.'Dir']);
		if (!$no && $oFile) {//combine files
			$realPath	=	realpath($oFile);
			$storeVar	=	$type.'Files';
			
			$combineself	=	BaseTag::getAttr('combineself',$tags);
			$staticNum		=	'_'.$type;
			$theNum			=	0;
			if ($combineself) {
				$theNum	=	++$$staticNum;
				$this->{$storeVar}[$theNum][]	=	$realPath;
				return "<!--###$type $theNum #smile-->";
			}else {
				$this->{$storeVar}[0][]		=	$realPath;
				if (count($this->{$storeVar}[0]) == 1) {
					return "<!--###$type $theNum #smile-->";
				}
			}
		}else {
			$attr		=	BaseTag::buildAttr($tags);
			if('css' == $type)
			{
				return "<link{$attr} />";
			}else {
				return "<script{$attr}></script>";
			}
		}
	}
	
	function reCombine($id,$type) {
		/*$minifGroup	=	include $this->minifyGroupsConfigFilePath;
		is_array($minifGroup) || $minifGroup = array();
		$writeFile	=	FALSE;
		foreach (array('css','js') as $value) {
			$storeVar	=	$value.'Files';
			if ($this->$storeVar) {
				$key		=	array_search($this->$storeVar,$minifGroup);
				if (FALSE === $key) {//generate new key
					$writeFile	=	TRUE;
					$key		=	uniqid();
					$minifGroup[$key]	=	$this->$storeVar;
				}
				if ('css' == $value) {
					$replace	=	'<link type="text/css" rel="stylesheet" href="'.$this->minifyURL.'g='.$key.'" />';
				}else {
					$replace	=	'<script type="text/javascript" src="'.$this->minifyURL.'g='.$key.'"></script>';
				}
				//replace the content
				$content	=	str_replace("<!--###$value#smile-->",$replace,$content);
			}
			$this->$storeVar	=	array();
		}
		if ($writeFile) {
			$fileC	=	'<?php return '.var_export($minifGroup,TRUE).';';
			file_put_contents($this->minifyGroupsConfigFilePath,$fileC);
		}*/
		$storeVar	=	$type.'Files';
		$key		=	array_search($this->{$storeVar}[$id],$this->minifGroup);
		if (FALSE === $key) {
			$this->writeMinifConfig	=	TRUE;
			$key					=	uniqid();
			$this->minifGroup[$key]	=	$this->{$storeVar}[$id];
		}
		if ('css' == $type) {
			return '<link type="text/css" rel="stylesheet" href="'.$this->minifyURL.'g='.$key.'" />';
		}elseif ('js' == $type){
			return '<script type="text/javascript" src="'.$this->minifyURL.'g='.$key.'"></script>';
		}
	}
	
	private function exeFile($attr,$src,$dir) {
		stripPreg($attr);
		
		$tags		=	BaseTag::getAttrArray($attr);
		$href		=	$tags[$src];
		$noPhp		=	BaseTag::getAttr('nophp',$tags);
		$oFile		=	$this->getExternalFilePath($href,$dir);
		
		if (!$noPhp && $oFile) {
			$nFile	=	$this->getNewExternalFilePath($oFile,'p.');
			if (!file_exists($nFile) || filemtime($oFile) > filemtime($nFile)) {
				$st		=	Smile::getInstance('SmileTemplate',TRUE);
				$content	=	file_get_contents($oFile);
				$content=	$st->toHtml($content);		
				writeFile($nFile,$content);
			}
			$tags[$src]	=	$this->getNewExternalFilePath($href,'p.');
		}
		$attr	=	BaseTag::buildAttr($tags);
		return $attr;
	}
	
	private function getExternalFilePath($src,$dirs) {
		if (!$dirs) {
			return FALSE;
		}
		if (is_string($dirs)) {
			$dirs	=	explode(',',$dirs);
		}
		foreach ($dirs as $value) {
			$aDir	=	str_replace('\\','/',$value);
			$aDir	=	rtrim($aDir,'/').'/';
			$newScr	=	ltrim($src,'/');
			$arrSrc	=	explode('/',$newScr);
			$arrSrc	=	array_filter($arrSrc,'strlen');
			for ($i=0,$_count=count($arrSrc);$i<$_count;++$i){
				$sub		=	array_slice($arrSrc,$i);
				$oPath	=	$aDir.implode('/',$sub);
				if (!file_exists($oPath)) {
					continue;
				}
				return $oPath;
			}
		}
		return FALSE;
	}
	
	private function getNewExternalFilePath($old,$prefix='s.') {
		$expOld	=	explode('.',$old);
		$expOld[count($expOld)-1]	=	$prefix.$expOld[count($expOld)-1];
		return implode('.',$expOld);
	}
}