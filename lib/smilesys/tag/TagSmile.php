<?php
Smile::requireOnce (SMILE_SYS_PATH.'tag/BaseTag.php');
class TagSmile extends BaseTag {

	protected $tags	=	array(
		'if'		=>	array(
			'nested'	=>	1,
			'attr'		=>	1,
		),
		'php'		=>	array(
			'content'	=>	1,
		),
		'elseif'	=>	array(
			'attr'		=>	1,
		),
		'else'		=>	array(),
		'switch'	=>	array(
			'attr'		=>	1,
			'content'	=>	1,
		),
		'case'		=>	array(
			'attr'	=>	1,
			'content'	=>	1,
		),
		'default'	=>	array(),
		'present'	=>	array(
			'attr'	=>	1,
			'nested'	=>	1,
		),
		'notpresent'=>	array(
			'attr'	=>	1,
			'nested'	=>	1,
		),
		'empty'		=>	array(
			'attr'	=>	1,
			'nested'	=>	1,
		),
		'notempty'	=>	array(
			'attr'	=>	1,
			'nested'	=>	1,
		),
		'defined'	=>	array(
			'attr'	=>	1,
			'nested'	=>	1,
		),
		'notdefined'	=>	array(
			'attr'	=>	1,
			'nested'	=>	1,
		),
		'include'		=>	array(
			'attr'	=>	1,
		),
		'foreach'		=>	array(
			'attr'	=>	1,
			'nested'	=>	1,
		),
		'for'			=>	array(
			'attr'	=>	1,
			'nested'	=>	1,
		),
		'volist'		=>	array(
			'attr'	=>	1,
			'nested'	=>	1,
		),
		'iterate'		=>	array(
			'attr'	=>	1,
			'nested'	=>	1,
		),
		'nohtmlcache'	=>	array(
			'content'	=>	1,
		),
		array(
			'attr'	=>	1,
			'name'=>	'nohtmlcache',
		),
		'htmlcache'	=>	array(
			'content'	=>	1,
		),
		array(
			'attr'	=>	1,
			'name'=>	'htmlcache',
		),
		'block'		=>	array(
			'attr'	=>	1,
			'content'	=>	1,
		),
		array(
			'attr'	=>	1,
			'name'=>	'block',
		),
		'blockto'	=>	array(
			'attr'	=>	1,
		),
		'widget'	=>	array(
			'attr'	=>	1,
		),
	);
	
	function _if($attr,$content) {
//		$content	=	stripslashes($content);
		$attr		=	self::getSingleAttr($attr,'condition,c');
		$condition	=	trim(self::condition($attr));
		return "<?php if($condition){?>{$content}<?php }?>";
	}
	function _elseif($attr) {
		$attr		=	self::getSingleAttr($attr,'condition,c');
		$condition	=	trim(self::condition($attr));
		return "<?php }elseif($condition){ ?>";
	}
	function _else() {
		return '<?php }else{?>';
	}
	
	
	
	function _php($attr,$content) {
//		$content	=	stripslashes($content);
		return "<?php $content?>";
	}
	
	function _switch($attr,$content) {
//		$content	=	stripslashes($content);
		$name	=	self::getSingleAttr($attr,'name,n');
		$varArray = explode('|',$name);
        $name   =   array_shift($varArray);
        $name = self::autoBuildVar($name);
        if(count($varArray)>0)
            $name = $this->tpl->_varFunction($name,$varArray);
        $parseStr = '<?php switch('.$name.'){?>'.$content.'<?php }?>';
        return $parseStr;
		
	}
	
	function _case($attr,$content) {
		$tag		=	self::getAttrArray($attr);
		if (is_array($tag) && count($tag)>1) {
			$value	=	$tag['value']?$tag['value']:$tag['v'];
			$br		=	$tag['break']?$tag['break']:$tag['b'];
		}else{
	        $value	=	self::getSingleAttr($attr,'value,v');
		}
        if('$' == substr($value,0,1)) {
            $varArray = explode('|',$value);
            $value	=	array_shift($varArray);
            $value  =  self::autoBuildVar(substr($value,1));
            if(count($varArray)>0)
                $value = $this->tpl->_varFunction($value,$varArray);
            $value   =  'case '.$value.': ';
        }elseif(strpos($value,'|')){
            $values  =  explode('|',$value);
            $value   =  '';
            foreach ($values as $val){
                $value   .=  'case "'.addslashes($val).'": ';
            }
        }else{
            $value	=	'case "'.$value.'": ';
        }
        $parseStr = '<?php '.$value.'?>'.$content;
        if(empty($br)) {
            $parseStr .= '<?php break;?>';
        }
        return $parseStr;
    }
    
	function _default() {
        return '<?php default: ?>';
    }
	
    function _present($attr,$content) {
    	$name	=	self::getSingleBuildVar($attr,'name,n');
        return '<?php if(isset('.$name.')){?>'.$content.'<?php }?>';
    }
	function _notpresent($attr,$content)
    {
        $name	=	self::getSingleBuildVar($attr,'name,n');
        return '<?php if(!isset('.$name.')){?>'.$content.'<?php }?>';
    }
    
    function _empty($attr,$content) {
    	$name	=	self::getSingleBuildVar($attr,'name,n');
    	return "<?php if(empty($name)){?>$content<?php }?>";
    }
	function _notempty($attr,$content) {
    	$name	=	self::getSingleBuildVar($attr,'name,n');
    	return "<?php if(!empty($name)){?>$content<?php }?>";
    }
    
    function _defined($attr,$content){
    	$name	=	self::getSingleAttr($attr,'name,n');
    	return "<?php if(defined($name)){?>$content<?php }?>";
    }
    function _notdefined($attr,$content){
    	$name	=	self::getSingleAttr($attr,'name,n');
    	return "<?php if(!defined($name)){?>$content<?php }?>";
    }
    
	function _include($attr)
    {
        $file	=	self::getSingleAttr($attr,'file,f');
        return $this->tpl->_include($file);
    }
    
	function _foreach($attr,$content)
    {
        static $_iterateParseCache = array();
        //cache
        $cacheIterateId = md5($attr.$content);
        if(isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
        $tag   = self::getAttrArray($attr);
    	if (!is_array($tag)) {
        	$tag	=	array(
        		'name'	=>	trim($tag),
        	);
        }
        $name	=	isset($tag['name'])?$tag['name']:$tag['n'];
        $item	=	isset($tag['item'])?$tag['item']:(isset($tag['i'])?$tag['i']:'v');
        $key	=   isset($tag['key'])?$tag['key']:(isset($tag['k'])?$tag['k']:'k');
        $name	=	self::autoBuildVar($name);
        $parseStr  =  '<?php if(is_array('.$name.')){foreach('.$name.' as $'.$key.'=>$'.$item.'){?>';
        $parseStr	.=	$content;
//        $parseStr .= $this->tpl->compile($content);
        $parseStr .= '<?php }}?>';
        $_iterateParseCache[$cacheIterateId] = $parseStr;
        if(!empty($parseStr)) {
            return $parseStr;
        }
        return ;
    }
	function _volist($attr,$content)
    {
        static $_iterateParseCache = array();
        //cache
        $cacheIterateId = md5($attr.$content);
        if(isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
        $tag   = self::getAttrArray($attr);
        if (!is_array($tag)) {
        	$tag	=	array(
        		'name'	=>	trim($tag),
        	);
        }
        $name   =	$tag['name']?$tag['name']:$tag['n'];
        $id		=	$tag['id']?$tag['id']:'v';
        $empty	=	isset($tag['empty'])?$tag['empty']:'';
        $i		=   !empty($tag['i'])?$tag['i']:'i';
        $key	=	!empty($tag['key'])?$tag['key']:(!empty($tag['k'])?$tag['k']:'k');
        $mod    =   isset($tag['mod'])?$tag['mod']:'2';
        $name   = self::autoBuildVar($name);
        $parseStr  =  '<?php if(is_array('.$name.')){ $'.$i.' = 0;';
		if(isset($tag['length']) && '' !=$tag['length'] ) {
			$parseStr  .= ' $__LIST__ = array_slice('.$name.','.$tag['offset'].','.$tag['length'].');';
		}elseif(isset($tag['offset'])  && '' !=$tag['offset']){
            $parseStr  .= ' $__LIST__ = array_slice('.$name.','.$tag['offset'].');';
        }else{
            $parseStr .= ' $__LIST__ = '.$name.';';
        }
        $parseStr .= 'if( count($__LIST__)==0 )echo "'.$empty.'";';
        $parseStr .= 'else{ ';
        $parseStr .= 'foreach($__LIST__ as $'.$key.'=>$'.$id.'){ ';
        $parseStr .= '++$'.$i.';';
        $parseStr .= '$mod = ($'.$i.' % '.$mod.' );?>';
        $parseStr .= $content;
//        $parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php }}}else{echo "'.$empty.'" ;} ?>';
        $_iterateParseCache[$cacheIterateId] = $parseStr;

        if(!empty($parseStr)) {
            return $parseStr;
        }
        return ;
    }
    function _iterate($attr,$content){
    	return $this->_volist($attr,$content);
    }
    function _for($attr,$content){
    	$tag   = self::getAttrArray($attr);
    	if (!is_array($tag)) {
    		$tag	=	array(
    			'name'	=>	$tag,
    			'index'	=>	'i',
    			'count'	=>	'_c',
    			'id'	=>	'v',
    		);
    	}
    	$name	=	isset($tag['name'])?$tag['name']:$tag['n'];
    	$index	=	isset($tag['index'])?$tag['index']:(isset($tag['i'])?$tag['i']:'i');
    	$count	=	isset($tag['count'])?$tag['count']:(isset($tag['c'])?$tag['c']:'_c');
    	$id		=	isset($tag['id'])?$tag['id']:'v';
    	
    	$parseStr=	"<?php for(\${$index}=0,\${$count}=count(\${$name});\${$index}<\${$count};++\${$index}){ \${$id}=\${$name}[\${$index}];?>";
    	$parseStr.=	$content.'<?php }?>';
    	return $parseStr;
    }
    
    function _htmlCache($attr,$content) {
    	$name	=	self::getSingleAttr($attr,'file,f');
    	return $this->tpl->_htmlCache($name,$content);
    }
    
    function _noHtmlCache($attr,$content){
    	$name	=	self::getSingleAttr($attr,'file,f');
    	return $this->tpl->_NoHtmlCache($name,$content);
    }
    
    /**
     * parse assign tag
     * eg. <assign name=n value=v cache=c file=f />
     * eg. <assign name=n cache=1>return 1</assign>
     * @param $attr attribute
     * @param $content tag content
     * @return string
     */
    function _assign($attr,$content='') {
    	$tag	=	self::getAttrArray($attr);
    	$name	=	$tag['name']?$tag['name']:$tag['n'];
    	$value	=	$tag['value']?$tag['value']:$tag['v'];
    	$file	=	$tag['file']?$tag['file']:$tag['f'];
    	$cache	=	$tag['cache']?$tag['cache']:$tag['c'];
    	if ($cache) {
    		if ($file || $content) {
	    		$data	=	$this->tpl->exe($file,$content);
    		}elseif($value) {
    			$data	=	$this->tpl->get($value);
    		}else {
    			$data	=	$this->tpl->get($name);
    		}
    		$value	=	"unserialize('".serialize($data)."')";
    	}else {
    		$value	=	'"'.$value.'"';
    	}
    	return "<?php \$$name=$value;?>";
    }
    
    function _block($attr,$content='') {
    	static $i	=	0;
    	$tag			=	self::getAttrArray($attr);
    	if (is_array($tag)) {
	    	$tag['file']	=	isset($tag['file'])?$tag['file']:(isset($tag['f'])?$tag['f']:NULL);
	    	$tag['to']		=	isset($tag['to'])?$tag['to']:(isset($tag['t'])?$tag['t']:NULL);
    	}else {
    		$tag	=	array('to'=>$tag);
    	}
    	$tag['name']	=	isset($tag['name'])?$tag['name']:(isset($tag['n'])?$tag['n']:NULL);
    	$tag['name']	=	isset($tag['name'])?$tag['name']:'noNameBlock_'.++$i;
    	
    	return $this->tpl->block($tag,$content);
    }
    
    function _blockto($attr) {
    	$attr		=	self::getSingleAttr($attr,'name,n');
    	return "<!--###block $attr smile###-->";
    }
    
    function _widget($attr) {
    	$tag	=	self::getAttrArray($attr);
    	$name	=	self::getAttr('name', $tag);
    	$name	||	$name	=	'Widget';
    	$this->tpl->addDependWedget('Widget');
    	$this->tpl->addDependWedget($name);
    	$ret	=	'<?php $__widget=new '.$name;
    	if ($tag) {
    		$ret.=	'('.var_export($tag,TRUE).')';
    	}
    	$ret	.=	';echo $__widget->render();?>';
    	return $ret;
    }
}

