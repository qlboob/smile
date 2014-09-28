<?php
function compressCode($mixCode,$compress=array()) {
	$block	=	extractPhpAndHtml($mixCode);
	$flag	=	FALSE;
	foreach ($compress as $value) {
		$value && $flag=TRUE;
	}
	if ($flag) {
		$ret	=	'';
		for ($i = 0,$_count=count($block); $i < $_count; ++$i) {
			if ('html' == $block[$i]['0']) {
				$compress['html']	&& $block[$i]['1']	= trimHtml($block[$i]['1']);
				$htmlCode	=	$block[$i]['1'];
				if ($compress['js'] && stripos($htmlCode,'</script>')) {
					$htmlCode	=	preg_replace('/<script(.*?)>(.*?)<\/script>/eis',"trimJs('\\1','\\2')",$htmlCode);
				}
				if ($compress['jsFile'] && stripos($htmlCode,'</script>')) {
					$htmlCode	=	preg_replace('/<script(.*?)><\/script>/eis',"trimJsFile('\\1')",$htmlCode);
				}
				if ($compress['css'] && stripos($htmlCode, '</style>')) {
					$htmlCode	=	preg_replace('#<style(.*?)>(.*?)</style>#eis',"trimCss('\\1','\\2')",$htmlCode);
				}
				if (trim($htmlCode) || FALSE !== strpos($htmlCode,' ')) {
					$ret	.=	$htmlCode;
				}
			}else {
				if ($compress['php']) {
					$ret.=strip_whitespace($block[$i]['1']);
				}else {
					$ret.=$block[$i]['1'];
				}
			}
		}
		return $ret;
	}else {
		return $mixCode;
	}
}


function strip_whitespace($content) {
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
// from http://www.developpez.net/forums/d786381/php/langage/fonctions/analyser-fichier-php-token_get_all/
function extract_php_blocks($arg_path)
{
	$dbg=true;
	if(!is_file($arg_path)) exit ("extract_php_blocks() : ".$arg_path." file not found");
	$handle = @fopen($arg_path, "r");
	if ($handle) {
		$bloc = array();
		$php_bloc_number=0; //fisrt bloc = 0
		$line_number=1; // first row number  = 1
		$multiline_php_code="";
		$double_increment=false;
		$in_php_code=false;
		$in_single_quoted=false;
		$in_double_quoted=false;
		$in_comment_multi_line=false;
		$in_comment_single_line=false;
		while (!feof($handle)) {
			$line_mixed_code= fgets($handle, 4096);
			$nb_char=strlen($line_mixed_code);
			$i_inline=0;
			while($i_inline<$nb_char)
			{// scanning line
				if(!$in_php_code)
				{// searching php open tag : with strpos
					if($dbg) echo "\n".$line_number."---- OUT PHP ----\n".substr($line_mixed_code,$i_inline);
					$pos_open_tag = strpos(substr($line_mixed_code,$i_inline) ,'<?php');
					if ($pos_open_tag !== false)
					{
						$i_inline+=$pos_open_tag;
						$bloc[$php_bloc_number]=array("start_line" => $line_number,"start_pos" => $i_inline,"end_line" => null,"end_pos" => null,"code" => null);
						if($dbg) echo "\n".$line_number."-".$i_inline."\n";
						$in_php_code=true;
					}
				}// end  searching php open tag
				else
				{// searching php close tag : char by char
					$multiline_php_code.=$line_mixed_code[$i_inline];
					if($double_increment){$i_inline++;$multiline_php_code.=$line_mixed_code[$i_inline];$double_increment=false;	}
					if($dbg) echo "\n".$line_number."---- IN PHP ----\n".substr($line_mixed_code,$i_inline);
					if($dbg&&$in_comment_single_line) echo "\n [in_comment_single_line] \n";
 
					// specification in http://fr.php.net/manual/fr/language.types.string.php
					if($in_comment_multi_line)
					{// search ending sequence
						if($dbg) echo "\n [in_comment_multi_line] \n";
						if (($line_mixed_code[$i_inline]=='*')&&(($i_inline+1)<$nb_char)&&($line_mixed_code[$i_inline+1]=='/')){$in_comment_multi_line=false;;$double_increment=true;}
					}
					elseif($in_single_quoted)
					{// search ending sequence 
						if($dbg) echo "\n [in_single_quoted] \n";
						if (($line_mixed_code[$i_inline]=='\\')&&(($i_inline+1)<$nb_char)&&(
						($line_mixed_code[($i_inline+1)]=='\'')||($line_mixed_code[$i_inline+1]=='\\')
						))$double_increment=true;
						elseif ($line_mixed_code[$i_inline]=='\'') $in_single_quoted=false;
					}
					elseif($in_double_quoted)
					{// search ending sequence
						if($dbg) echo "\n [in_double_quoted] \n";
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
						$bloc[$php_bloc_number]["end_line"] = $line_number ;
						$bloc[$php_bloc_number]["end_pos"] = $i_inline+2;
						$bloc[$php_bloc_number]["code"] = '<'.$multiline_php_code.'>';
						// reset in_php_code values
						$multiline_php_code="";
						$php_bloc_number++;
						$in_php_code=false;
						$in_comment_single_line=false;
						$double_increment=true;
					}
					elseif((!$in_comment_multi_line)&&!$in_comment_single_line)
					{// not in comment
						if ($line_mixed_code[$i_inline]=='\'') $in_single_quoted=true;
						elseif ($line_mixed_code[$i_inline]=='"') $in_double_quoted=true;
						elseif ($line_mixed_code[$i_inline]=='#') $in_comment_single_line=true;
						elseif (($i_inline+1)<$nb_char)
						{// next char exist
							if($dbg) echo "\n [found char+1] \n".substr($line_mixed_code,$i_inline);
							if (($line_mixed_code[$i_inline]=='/')&&($line_mixed_code[($i_inline+1)]=='*')) {$in_comment_multi_line=true;;$double_increment=true;}
							elseif (($line_mixed_code[$i_inline]=='/')&&($line_mixed_code[($i_inline+1)]=='/')) {$in_comment_single_line=true;;$double_increment=true;}
						}// end next char exist
					}//end :  not in comment
					
				}// end  searching php close tag
				$i_inline++;
			}// end scanning line
			$line_number++;
			if($dbg) echo "\n [newline] \n";
			$in_comment_single_line=false;
		}
		fclose($handle);
	}
	return $bloc;
}
 


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
						if (substr($lastPhpCode,-1)!=';' && '}'==substr($lastPhpCode,-1)) {
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
//		echo $line_mixed_code;
	}
	return $bloc;
}
function msubstr($str, $start=0, $length, $charset="utf-8")
{
	if(function_exists("mb_substr"))
		return mb_substr($str, $start, $length, $charset);
	elseif(function_exists('iconv_substr')) {
		return iconv_substr($str,$start,$length,$charset);
	}
	$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
	$re['gbk']	  = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
	$re['big5']	  = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
	preg_match_all($re[$charset], $str, $match);
	$slice = join("",array_slice($match[0], $start, $length));
	return $slice;
}
function mstrlen($string, $charset="utf-8"){
	if(function_exists("mb_strlen"))
		return mb_strlen($string,$charset);
	else 
		return strlen($string);
}

function trimhtmlwhitespace($source)
{
    // Pull out the script blocks
    preg_match_all("!<script[^>]*?>.*?</script>!is", $source, $match);
    $_script_blocks = $match[0];
    $source = preg_replace("!<script[^>]*?>.*?</script>!is",
                           '<SMILE:TRIM:SCRIPT>', $source);

    // Pull out the pre blocks
    preg_match_all("!<pre[^>]*?>.*?</pre>!is", $source, $match);
    $_pre_blocks = $match[0];
    $source = preg_replace("!<pre[^>]*?>.*?</pre>!is",
                           '<SMILE:TRIM:PRE>', $source);
    
    // Pull out the textarea blocks
    preg_match_all("!<textarea[^>]*?>.*?</textarea>!is", $source, $match);
    $_textarea_blocks = $match[0];
    $source = preg_replace("!<textarea[^>]*?>.*?</textarea>!is",
                           '<SMILE:TRIM:TEXTAREA>', $source);

    // remove all leading spaces, tabs and carriage returns NOT
    // preceeded by a php close tag.

     /*$find     = array("~>\s+<~","~>(\s+\n|\r)~");
            $replace  = array("><",">");
            $source = preg_replace($find, $replace, $source);*/
    $source	=	preg_replace("~>\s+<~", '><', $source);
    
    // replace textarea blocks
    trimhtmlwhitespace_replace("<SMILE:TRIM:SCRIPT>",$_script_blocks, $source);

    // replace pre blocks
    trimhtmlwhitespace_replace("<SMILE:TRIM:PRE>",$_pre_blocks, $source);

    // replace script blocks
    trimhtmlwhitespace_replace("<SMILE:TRIM:TEXTAREA>",$_textarea_blocks, $source);

    return $source;
}

function trimhtmlwhitespace_replace($search_str, $replace, &$subject) {
    $_len = strlen($search_str);
    $_pos = 0;
    for ($_i=0, $_count=count($replace); $_i<$_count; $_i++)
        if (($_pos=strpos($subject, $search_str, $_pos))!==false)
            $subject = substr_replace($subject, $replace[$_i], $_pos, $_len);
        else
            break;

}

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
		trimhtmlwhitespace_replace("<SMILE:TRIM:$key>",$$key, $source);
	}
	return $source;
}

function trimJs($attr,$content) {
	stripPreg($attr,$content);
	if ($content){
		$content	=	trimJsContent($content);
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
	return "<style{$attr}>".$css->print->plain().'</style>';
		
}

function trimJsFile($attr) {
	stripPreg($attr);
	$posSrc	=	stripos($attr, ' src="');
	if (FALSE !== $posSrc) {//there is an attribute named src
		$endSrc	=	strpos($attr,'"',$posSrc+7);
		$src	=	substr($attr,$posSrc+6,$endSrc-$posSrc-6);
		if (!strpos($src,':') && strtolower(substr($src,-3))=='.js') {
			$jsDir	=	sc('jsDir');
			if ($jsDir) {
				$jsDir	=	str_replace('\\','/',$jsDir);
				$jsDir	=	rtrim($jsDir,'/').'/';
				$newScr	=	ltrim($src,'/');
				$arrSrc	=	explode('/',$newScr);
				for ($i=0,$_count=count($arrSrc);$i<$_count;++$i){
					$sub		=	array_slice($arrSrc,$i);
					$jsOPath	=	$jsDir.implode('/',$sub);
					if (!file_exists($jsOPath)) {
						continue;
					}
					$extend		=	substr($jsOPath,-3);
					$jsNPath	=	substr($jsOPath,0,-3).'.s'.$extend;
					if (!file_exists($jsNPath) || filemtime($jsNPath)<filemtime($jsOPath)) {
						$content	=	file_get_contents($jsOPath);
						$content	=	trimJsContent($content);
						writeFile($jsNPath,$content);
					}
					
					$attr	=	str_ireplace(" src=\"$src\"",' src="'.substr($src,0,-3).'.s'.$extend.'"',$attr);
				}
			}
		}
	}
	return "<script{$attr}></script>";
}