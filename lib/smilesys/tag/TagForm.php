<?php
Smile::requireOnce (SMILE_SYS_PATH.'tag/BaseTag.php');
class TagForm extends BaseTag {
	
	protected $tags	=	array(
		'input'			=>	array(
			'attr'		=>	1,
		),
		'field'			=>	array(
			'attr'		=>	1,
		),
		'password'		=>	array(
			'attr'		=>	1,
		),
		'hidden'		=>	array(
			'attr'		=>	1,
		),
		'radios'		=>	array(
			'attr'		=>	1,
		),
		'radio'			=>	array(
			'attr'		=>	1,
		),
		'checkboxs'		=>	array(
			'attr'		=>	1,
		),
		'checkbox'		=>	array(
			'attr'		=>	1,
		),
		'textarea'		=>	array(
			'attr'		=>	1,
		),
		'select'		=>	array(
			'attr'		=>	1,
		),
		array(
			'attr'	=>	1,
			'name'	=>	'presenthidden',
			'content'=>	1,
		),
		'presenthidden'	=>	array(
			'attr'		=>	1,
		),
	);
	/**
	 * parse input tag
	 * eg. <form:input name />
	 * @param string $attr
	 * @param string $type type of input
	 * @return string
	 */
	function _input($attr,$type) {
		$tag	=	$this->getAttrArray($attr);
		is_string($tag) && $tag = array('name'=>$tag);
		$type && $tag['type']	=	$type;
		$tag	=	$this->decorateAttr($tag,'id,value');
		$inputAttr	=	$this->buildAttr($tag);
		return "<input{$inputAttr} />";
	}
	
	/**
	 * parse field tag
	 * eg. <form:field name />
	 * @param $attr attribute
	 * @return string
	 */
	function _field($attr) {
		return  $this->_input($attr,'text');
	}
	
	function _password($attr) {
		if (strpos($attr,'"')) {
			$attr	.=	' value=""';
		}elseif (strpos($attr,"'")) {
			$attr	.=	" value=''";
		}
		return  $this->_input($attr,'password');
	}
	
	/**
	 * parse hidden tag
	 * eg. <form:hidden name />
	 * @param $attr
	 * @return string
	 */
	function _hidden($attr) {
		return $this->_input($attr,'hidden');
	}
	
	/**
	 * parse presenthidden
	 * eg. <form:presenthidden name />
	 * @param $attr
	 * @param $content
	 * @return string
	 */
	function _presenthidden($attr,$content) {
		$tag	=	$this->getAttrArray($attr);
		is_string($tag) && $tag = array('name'=>$tag);
		return "<?php if(isset(\${$tag['name']})){?>".$this->_hidden($attr).$content.'<?php }?>';
	}
	
	function _radios($attr) {
		return $this->group($attr,'radio');
	}
	
	/**
	 * parse radio
	 * eg. <form:radio name>
	 * @param $attr
	 * @return string
	 */
	function _radio($attr) {
		return $this->_input($attr,'radio');
	}
	
	/**
	 * parse checkbox
	 * eg. <form:checkbox name>
	 * @param $attr
	 * @return string
	 */
	function _checkbox($attr) {
		$tag	=	$this->getAttrArray($attr);
		is_string($tag) && $tag = array('name'=>$tag);
		$tag['type']	=	'checkbox';
		$tag	=	$this->decorateAttr($tag,'id,value');
		$condition	=	"<?php if(isset(\${$tag['name']}) and \${$tag['name']})echo ' checked=\"checked\"';?>";
		$attrStr	=	$this->buildAttr($tag);
		if (!isset($tag['nohidden'])) {
//			$addArr	=	$this->_hidden(array('name'=>$tag['name'],'value'=>isset($tag['hiddenvalue'])?$tag['hiddenvalue']:0,'id'=>''));
			$addArr	=	'<input type="hidden"'.$this->buildAttr(array('name'=>$tag['name'],'value'=>isset($tag['hiddenvalue'])?$tag['hiddenvalue']:0)).' />';
		}else 
			$addArr	=	'';
		return $addArr."<input{$attrStr}{$condition} />";
	}
	
	function _checkboxs($attr) {
		return $this->group($attr,'checkbox');
	}
	
	private function group($attr,$type) {
		$ret	=	'';
		$tag	=	$this->getAttrArray($attr);
		$fetch	=	array('name','selected','default','html','keyhtml','valuehtml','callback','separator');
		foreach ($fetch as $v) {
			$$v	=	$this->getAttr($v,$tag);
		}
		$options	=	$this->combineData($tag);
		if('checkbox'==$type && '[]'!=substr($name,-2))
			$name.='[]';
		$tag['name']	=	$name;
		$tag['type']	=	$type;
		
		$container = '$__CONT__';
		$inputAttr = $this->buildAttr($tag,FALSE);
		
		$keyFun		=	'htmlspecialchars';
		$valueFun	=	'htmlspecialchars';
		if ($html) {
			$keyFun		=	'';
			$valueFun	=	'';
		}elseif ($keyhtml){
			$keyFun		=	'';
		}elseif ($valuehtml){
			$valueFun	=	'';
		}
		
		$selected&&$default	&& $ret.=	"<?php \$$selected||\$$selected=\"$default\";?>";
		$ret .= '<?php '.$container.'=array();foreach('.$options.' as $key=>$val) { ?>';
        if ($selected) {
        	$ret .= '<?php if(isset($'.$selected.')&&($'.$selected.'==$key||(is_array($'.$selected.')&&in_array($key,$'.$selected.')))){ ?>';
        	$ret .= "<?php {$container}[]='<label><input{$inputAttr} checked=\"checked\" value=\"'.{$keyFun}(\$key).'\" />'.{$valueFun}(\$val).'</label>';?>";
        	$ret .= '<?php }else{?>'."<?php {$container}[]='<label><input {$inputAttr} value=\"'.{$keyFun}(\$key).'\" />'.{$valueFun}(\$val).'</label>';?>";
        	$ret .= '<?php }?>';
        }else {
        	$ret .= "<?php {$container}[]='<label><input {$inputAttr} value=\"'.{$keyFun}(\$key).'\" />'.{$valueFun}(\$val).'</label>';?>";
        }
        $ret   .= '<?php }?>';
        if (empty($callback)) {
        	$ret .="<?php echo implode('".addslashes($separator)."',$container);?>";
        }else {
        	$this->tpl->addDependFun($callback);
        	$ret .= "<?php echo $callback($container);?>";
        }
        return $ret;
	}
	
	/**
	 * parse textarea
	 * eg. <form:textarea name>
	 * @param string $attr
	 * @return string
	 */
	function _textarea($attr) {
		$tag	=	$this->getAttrArray($attr);
		is_string($tag) && $tag = array('name'=>$tag);
		$html	=	$this->getAttr('html',$tag);
		$value	=	$this->getAttr('value',$tag,'$'.$tag['name']);
		$tag['id']	=	$tag['id']?$tag['id']:$tag['name'];
		$textAreaAttr	=	$this->buildAttr($tag);
		if ($html) {
			if($value[0]=='$')
				$parsedValue	=	"<?php if(isset($value)) echo $value;?>";
			else 
				$parsedValue	=	$value;
		}else {
			if($value[0]=='$')
				$parsedValue	=	"<?php if(isset($value)) echo htmlspecialchars({$value});?>";
			else 
				$parsedValue	=	htmlspecialchars($value);
		}
		return "<textarea{$textAreaAttr}>{$parsedValue}</textarea>";
	}
	
	function _select($attr) {
		$ret	=	'';
		$tag	=	$this->getAttrArray($attr);
		//extract var
		$fetch	=	array('selected','default','html','keyhtml','valuehtml','first');
		foreach ($fetch as $v) {
			$$v	=	$this->getAttr($v,$tag);
		}
		$optionV	=	$this->combineData($tag);
		//set default value
		$selected&&$default	&& $ret.=	"<?php \$$selected||\$$selected=\"$default\";?>";
		$attrStr	=	$this->buildAttr($tag,$html);
		$ret		.=	"<select{$attrStr}>";
		
		$keyFun		=	'htmlspecialchars';
		$valueFun	=	'htmlspecialchars';
		if ($html) {
			$keyFun		=	'';
			$valueFun	=	'';
		}elseif ($keyhtml){
			$keyFun		=	'';
		}elseif ($valuehtml){
			$valueFun	=	'';
		}
		
		$first		&&	$ret	.=	'<option value="">'.$first.'</option>';
		if (!empty($optionV)) {
			$ret .= '<?php foreach('.$optionV.' as $key=>$val){?>';
        	if(!empty($selected)) {
        		//0=='' 所以要区分一 下这个情况
                $ret   .= '<?php if(isset($'.$selected.')&&($'.$selected.'==$key&&$key!==0&&\'\'!==$'.$selected.'||(is_array($'.$selected.')&&in_array($key,$'.$selected.')))){?>';
                $ret   .= "<option selected=\"selected\" value=\"<?php echo $keyFun(\$key); ?>\"><?php echo $valueFun(\$val);?></option>";
                $ret   .= "<?php }else{?><option value=\"<?php echo $keyFun(\$key);?>\"><?php echo $valueFun(\$val);?></option>";
                $ret   .= '<?php }?>';
            }else {
                $ret   .= "<option value=\"<?php echo $keyFun(\$key);?>\"><?php echo $valueFun(\$val);?></option>";
            }
        	$ret .= '<?php }?>';
		}
		return $ret.'</select>';
	}
	
	private function combineData(&$tag) {
		//fetch variable
		$fetch	=	array('options','optionsfile','values','valuesfile','output','outputfile','cache');
		foreach ($fetch as $v) {
			$$v	=	$this->getAttr($v,$tag);
		}
		if ($cache) {
			if ($optionsfile) {
				$data	=	$this->tpl->exe($optionsfile);
			}elseif($options) {
				$data	=	$this->tpl->get($options);
			}else{
				if ($valuesfile) {
					$valuesData	=	$this->tpl->exe($valuesfile);
				}elseif ($values) {
					$valuesData	=	$this->tpl->get($values);
				}
				
				if ($outputfile) {
					$outputData	=	$this->tpl->exe($outputfile);
				}elseif($output) {
					$outputData	=	$this->tpl->get($output);
				}else {
					$data	=	array_combine($valuesData,$valuesData);
				}
				$data || $data	=	array_combine($valuesData,$outputData);
			}
			$serData	=	serialize($data);
			return "unserialize('$serData')";
		}else {
			if ($options) {
				return '$'.$options;
			}elseif ($values){
				if (!$output) {
					$output	=	$values;
				}
				return 	"array_combine(\$$values,\$$output)";
			}
		}
	}
	
}