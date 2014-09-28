<?php

abstract class BaseTag {

	//smiletemplate
	protected $tpl;
	static protected $comparison = array(' nheq '=>'!==',' heq '=>'===',' neq '=>'!=',' eq '=>'==',' egt '=>'>=',' gt '=>'>',' elt '=>'<=',' lt '=>'<');
	
	protected $tags	=	array();
	
	function getTag() {
		return $this->tags;
	}
	
	function __construct($tpl) {
		$this->tpl	=	$tpl;
	}
	
	
	static function getAttrArray($attr) {
		//double quote eg. name="name"
		$patternDouble	=	'/(\w+?)\s*?=\s*?"(.*?)"/is';
		$patternSingle	=	'/(\w+?)\s*?=\s*?\'(.*?)\'/is';
		$patternNo		=	'/(\w+?)\s*?=\s*?(\S+)/is';
		(strpos($attr,'"') && preg_match_all($patternDouble,$attr,$matches,PREG_SET_ORDER) ) ||
		(strpos($attr,"'") && preg_match_all($patternSingle,$attr,$matches,PREG_SET_ORDER))||
		(strpos($attr,'=') && preg_match_all($patternNo,$attr,$matches,PREG_SET_ORDER)) ;
		if (isset($matches) && $matches) {
			foreach ($matches as $match) {
				$ret[$match['1']]	=	htmlspecialchars_decode($match['2']);
			}
			return $ret;
		}else {
			return trim($attr);
		}
	}
	static function getSingleAttr($attr,$sequence) {
		$result	=	self::getAttrArray($attr);
		if (is_array($result)) {
			if (is_string($sequence)) {
				$sequence	=	explode(',',$sequence);
			}
			foreach ($sequence as $v) {
				if (isset($result[$v]))
					return $result[$v];
			}
		}
		
		return trim($result);
		
	}
	static function getSingleBuildVar($attr,$sequence) {
		$var	=	self::getSingleAttr($attr,$sequence);
		return self::autoBuildVar($var);
	}
	
	
	static function condition($condition) {
		return str_ireplace(array_keys(self::$comparison),array_values(self::$comparison),$condition);
	}
	
	static function autoBuildVar($name) {
        if('Smile.' == substr($name,0,6)){
            // �����
            return self::parseSmileVar($name);
        }elseif(strpos($name,'.')) {
            $vars = explode('.',$name);
            $var  =  array_shift($vars);
            switch(strtolower(Smile::config('TMPL_VAR_IDENTIFY'))) {
                case 'array': // ʶ��Ϊ����
                    $name = '$'.$var;
                    foreach ($vars as $key=>$val)
                        $name .= '["'.$val.'"]';
                    break;
                case 'obj':  // ʶ��Ϊ����
                    $name = '$'.$var;
                    foreach ($vars as $key=>$val)
                        $name .= '->'.$val;
                    break;
                default:  // �Զ��ж��������� ֻ֧�ֶ�ά
                    $name = 'is_array($'.$var.')?$'.$var.'["'.$vars[0].'"]:$'.$var.'->'.$vars[0];
            }
        }elseif(strpos($name,':')){
            // ����Ķ���ʽ֧��
            $name   =   '$'.str_replace(':','->',$name);
        }elseif(!defined($name)) {
            $name = '$'.$name;
        }
        return $name;
    }

    /**
     +----------------------------------------------------------
     * ���ڱ�ǩ�������������ģ�������
     * ��ʽ �� Think. ��ͷ�ı���������ģ���
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $varStr  ���ַ�
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    static public function parseSmileVar($varStr){
    	$vars = explode('.',$varStr);
        array_shift($vars);
        $type = array_shift($vars);
        $type = strtoupper(trim($type));
        $parseStr = '';
        $count = count($vars);
        if($count>0){
            $smileA	=	"['".implode("']['",$vars)."']";
            switch($type){
                /*case 'SERVER':    $parseStr = '$_SERVER[\''.$vars[2].'\']';break;
                case 'GET':         $parseStr = '$_GET[\''.$vars[2].'\']';break;
                case 'POST':       $parseStr = '$_POST[\''.$vars[2].'\']';break;
                case 'COOKIE':    $parseStr = '$_COOKIE[\''.$vars[2].'\']';break;
                case 'SESSION':   $parseStr = '$_SESSION[\''.$vars[2].'\']';break;
                case 'ENV':         $parseStr = '$_ENV[\''.$vars[2].'\']';break;
                case 'REQUEST':  $parseStr = '$_REQUEST[\''.$vars[2].'\']';break;
                case 'CONST':     $parseStr = strtoupper($vars[2]);break;
                case 'LANG':       $parseStr = 'L("'.$vars[2].'")';break;
                case 'CONFIG':    $parseStr = 'C("'.$vars[2].'")';break;*/
                case 'SERVER':    $parseStr = '$_SERVER'.$smileA;break;
                case 'GET':         $parseStr = '$_GET'.$smileA;break;
                case 'POST':       $parseStr = '$_POST'.$smileA;break;
                case 'COOKIE':    $parseStr = '$_COOKIE'.$smileA;break;
                case 'SESSION':   $parseStr = '$_SESSION'.$smileA;break;
                case 'ENV':         $parseStr = '$_ENV'.$smileA;break;
                case 'REQUEST':  $parseStr = '$_REQUEST'.$smileA;break;
                case 'CONST':     $parseStr = implode('.',$vars);break;
                case 'LANG':       $parseStr = 'L("'.$vars[0].'")';break;
                case 'CONFIG':    $parseStr = 'C("'.$vars[0].'")';break;
            }
        }else{
            switch($type){
                case 'NOW':       $parseStr = "date('Y-m-d g:i a',time())";break;
                case 'VERSION':  $parseStr = 'THINK_VERSION';break;
                case 'TEMPLATE':$parseStr = 'C("TMPL_FILE_NAME")';break;
                case 'LDELIM':    $parseStr = 'C("TMPL_L_DELIM")';break;
                case 'RDELIM':    $parseStr = 'C("TMPL_R_DELIM")';break;
                default:  if(defined($vars[0])) $parseStr = $vars[0];
            }
        }
        return $parseStr;
    }
    
	/**
	 * add attr
	 * @param array $attr
	 * @param <string,array> $warp
	 */
	static function decorateAttr($attr,$warp) {
	    if (empty($warp)) {
	    	return $attr;
	    }
    	if (is_string($warp) ) {
    		if (strpos($warp,',')) {
    			$warp = explode(',',$warp);
    		}else {
    			$warp = array($warp);
    		}
    	}
    	foreach ($warp as $v) {
    		if (isset($attr['name']) && !isset($attr[$v])) {
	    		if ('value'==$v) {
			    		$attr[$v] = '$'.$attr['name'];
	    		}else {
			    		$attr[$v] = $attr['name'];
		    	}
    		}
    	}
    	return $attr;
    }
    
    /**
     * biuld html tag attribut
     * @param array $attr attr array
     * @param boolean $html htmlspecialchars
     */
	static function buildAttr($attr,$html=FALSE) {
		$ret = '';
		$html = $html?$html:self::getAttr('html',$attr);
		foreach ($attr as $k => $v) {
			if ('' !== $v) {
				$ret .=' ';
				if ($v['0']=='$') {
					if ($html) {
						/*$ret.=$k.'="<?php if(isset('.$v.')) echo '.$v.';?>"';*/
						$ret.=$k.sprintf('="<?php if(isset(%s)) echo %s(%s);?>"',$v,$html,$v);
					}else {
						$ret.=$k.'="<?php if(isset('.$v.')) echo htmlspecialchars('.$v.');?>"';
					}
				}else{
					$ret.=$k.'="'.htmlspecialchars($v).'"';
				}
			}
		}
		return $ret;
	}
	
	/**
	 * get the value of a key in the array and delete the key
	 * @param string $key
	 * @param string $attr
	 * @return string
	 */
	static function getAttr($key,&$attr,$default='') {
		if (isset($attr[$key])){
	    	$ret = $attr[$key];
	    	unset($attr[$key]);
		}
    	return	isset($ret)?$ret:$default;
    }
}

