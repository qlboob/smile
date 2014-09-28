<?php
Smile::requireOnce (SMILE_SYS_PATH.'tag/BaseTag.php');
class TagSortable extends BaseTag {
	
	protected $tags	=	array(
		'list'	=>	array('attr'=>1)
	);
	
	function _list($attr) {
		//get attribute
		$tag		=	$this->getAttrArray($attr);
		$fetchVar	=	array(
							'id'=>'pk',
							'vo'=>'vo',
							'sortBy'=>'colonmousedown',
							'checkbox',
							'sortby',//sort by columns eg. array('col1'=>1,'col2'=>0)
							'up',
							'down',
							'upimg',
							'downimg',
							'checkboxname',
							'datasource',
							'actionname',
							'actionlist',
							'show',
						);
		foreach ($fetchVar as $key => $value) {
			$$value	=	$this->getAttr($value,$tag,is_numeric($key)?'':$key);
			if (substr($$value, 0, 1) == '$') {
				$$value	=	$this->tpl->get(substr($$value, 1));
			}
		}
		if($upimg){
			$upimg		=	'<img border="0" scr="'.$upimg.'" />';
			$downimg	=	'<img border="0" scr="'.$downimg.'" />';
		}else {
			$upimg		=	$up;
			$downimg	=	$down;
		}
		
        //eg1. colX|PhpFunX:showNameX|widthX|titleX:jsFunX|agr1X^agr2X
        //colX:the key of datasource
        //PhpFunX:use php function to the value which is $datasource[colX]
        //showNameX:the content of th tag
        //widthX:the width of th tag
        //titleX:the title attribute of th tag
        //jsFunx:use js function to the column
        //agr1X:the argument of the js function,vaule is $datasource[agr1X]. if there is no argument,the argument is pk which value is $datasource[$pk]
        //eg2. colX|phpFunx^colY:showNameX|widthX|titleX:jsFunX|agr1X^agr2X      to display to mutiple columns
        //colX the first colY the second and so no...
		/*if (substr($show,0,1)=='$') {
        	$show   = $this->tpl->get(substr($show,1));
        }*/
        if (is_string($show))
        $show = explode(',',$show);
        
        //eg1. colX
        //colX:the colum name
        //eg2. colX|phpFunX
        //colX:the colum name
        //phpFunX: use php function to column
        //eg3. colX|jsFunX:showNameX
        //colX:the colum name
        //jsFunX: js function name ,will pass the $datasource[colX]
        //showNameX: display name
        //eg4. jsFunX:showNameX
        //jsFunX: js function , will pass the $datasource[$pk]
        //showNameX:display name
        if ($actionlist) {
        	/*if (substr($actionlist,0,1)=='$') {
        		$actionlist	=	$this->tpl->get(substr($actionlist, 1));
        	}*/
        	if (is_string($actionlist))
        	$actionlist = explode(',',$actionlist);
        }
        
        $checkboxname || $checkboxname='id';
        substr($checkboxname,-2)=='[]' || $checkboxname.='[]';
        
        //get attribute string
		$properties = array('th','tr','td','firstcheckbox');
        $propertiesStr = array();
        foreach ($properties as $v) {
        	$xxProperty = array();
        	$vLen = strlen($v);
        	foreach (array_keys($tag) as $k) {
	        	if (substr($k,0,$vLen) == $v) {
	        		$xxProperty[substr($k,$vLen)] = $this->getAttr($k,$tag);
	        	}
	        }
	        $propertiesStr[$v] = $this->buildAttr($xxProperty);
        }
        
        //calculate columns
        $colNum     = count($show);
        $checkbox && ++$colNum;
        $actionlist && ++$colNum;
        //start output
        $ret = '<table '.$this->buildAttr($tag).'>';
        $ret .= "<tr>";
		$fields = array();
        foreach($show as $key=>$val) {
        	$fields[] = explode(':',$val);
        }
        if ($checkbox) {
        	$ret .='<th width="8"'.$propertiesStr['th'].'><input type="checkbox"'.$propertiesStr['firstcheckbox'].'></th>';
        }
        foreach($fields as $field) {
            $property = explode('|',$field[0]);
            $showname = explode('|',isset($field[1])?$field[1]:'');
            if(isset($showname[1])) {//add column width & property
                $ret .= '<th width="'.$showname[1].'"'.$propertiesStr['th'].'>';
            }else {//add property
                $ret .= "<th{$propertiesStr['th']}>";
            }
            //title attribute
            $showname[2] = isset($showname[2])?$showname[2]:$showname[0];
//            $ret .= "<a href=\"javascript:sortBy('".$property[0].'\',\'{$sort}\',\''.ACTION_NAME.'\')" title="'.$showname[2].'{$sortType} ">'.$showname[0].'<eq name="order" value="'.$property[0].'" ><img src="../Public/images/{$sortImg}.gif" width="12" height="17" border="0" align="absmiddle"></eq></a></th>';
			//anchor & mousedown event
            $ret	.=	"<em onclick=\"return $colonmousedown(event,'{$property[0]}','{\${$sortby}['$property[0]']}')\">{$showname[0]}</em>";
            //sort img
            $ret	.=	"<present {$sortby}['$property[0]']><if \${$sortby}['$property[0]']>{$upimg}<else />{$downimg}</if></present>";
            $ret	.=	'</th>';//end a th tag
        }
        if(!empty($actionlist)) {
            $ret .= "<th {$propertiesStr['th']}>{$actionName}</th>";
        }
        $ret .= '</tr>';//first line end
        //to display data columns
		$ret .= '<volist name="'.$datasource.'" id="'.$vo.'" ><tr'.$propertiesStr['tr'].'>';
    	if(!empty($checkbox)) {
            $ret .= '<td '.$propertiesStr['td'].'><input type="checkbox" name="'.$checkboxname.'" value="{$'.$vo.'.'.$pk.'|htmlspecialchars}"></td>';
        }
        foreach($fields as $field) {
            $ret   .=  "<td{$propertiesStr['td']}>";
            if(!empty($field[2])) {
                $href = explode('|',$field[2]);
                if(count($href)>1) {
                    $array = explode('^',$href[1]);
                    if(count($array)>1) {
                        foreach ($array as $a){
                            $temp[] =  '\'{$'.$vo.'.'.$a.'|addslashes}\'';
                        }
                        $ret .= '<a href="javascript:'.$href[0].'('.implode(',',$temp).')">';
                    }else{
                        $ret .= '<a href="javascript:'.$href[0].'(\'{$'.$vo.'.'.$href[1].'|addslashes}\')">';
                    }
                }else {
                    $ret .= '<a href="javascript:'.$field[2].'(\'{$'.$vo.'.'.$pk.'|addslashes}\')">';
                }
            }
            if(strpos($field[0],'^')) {
                $property = explode('^',$field[0]);
                foreach ($property as $p){
                    $unit = explode('|',$p);
                    if(count($unit)>1) {
                        $ret .= '{$'.$vo.'.'.$unit[0].'|'.$unit[1].'} ';
                    }else {
                        $ret .= '{$'.$vo.'.'.$p.'} ';
                    }
                }
            }else{
                $property = explode('|',$field[0]);
                if(count($property)>1) {
                    $ret .= '{$'.$vo.'.'.$property[0].'|'.$property[1].'}';
                }else {
                    $ret .= '{$'.$vo.'.'.$field[0].'}';
                }
            }
            if(!empty($field[2])) {
                $ret .= '</a>';
            }
            $ret .= '</td>';

        }
    	if(!empty($actionlist)) {
            if(!empty($actionlist[0])) {
                $ret .= "<td {$propertiesStr['td']}>";
                foreach($actionlist as $val) {
					if(strpos($val,':')) {
						$a = explode(':',$val);
						$b = explode('|',$a[1]);
						if(count($b)>1) {
							$c = explode('|',$a[0]);
							if(count($c)>1) {
								$ret .= '<a href="javascript:'.$c[1].'(\'{$'.$vo.'.'.$pk.'}\')"><?php if(0== (is_array($'.$vo.')?$'.$vo.'["status"]:$'.$vo.'->status)){ ?>'.$b[1].'<?php } ?></a><a href="javascript:'.$c[0].'({$'.$vo.'.'.$pk.'})"><?php if(1== (is_array($'.$vo.')?$'.$vo.'["status"]:$'.$vo.'->status)){ ?>'.$b[0].'<?php } ?></a>&nbsp;';
							}else {
								$ret .= '<a href="javascript:'.$a[0].'(\'{$'.$vo.'.'.$pk.'}\')"><?php if(0== (is_array($'.$vo.')?$'.$vo.'["status"]:$'.$vo.'->status)){ ?>'.$b[1].'<?php } ?><?php if(1== (is_array($'.$vo.')?$'.$vo.'["status"]:$'.$vo.'->status)){ ?>'.$b[0].'<?php } ?></a>&nbsp;';
							}

						}else {
							$ret .= '<a href="javascript:'.$a[0].'(\'{$'.$vo.'.'.$pk.'}\')">'.$a[1].'</a>&nbsp;';
						}
					}else{
						$array	=	explode('|',$val);
						if(count($array)>2) {
							$ret	.= ' <a href="javascript:'.$array[1].'(\'{$'.$vo.'.'.$array[0].'}\')">'.$array[2].'</a>&nbsp;';
						}else{
							$ret .= ' {$'.$vo.'.'.$val.'}&nbsp;';
						}
					}
                }
                $ret .= '</td>';
            }
        }
        $ret .= '</tr></volist></table>';
        return $ret;
	}
}