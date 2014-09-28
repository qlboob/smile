


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta charset="utf-8" />
		<title>管理系统--</title>
		<link rel="stylesheet" href="http://st.midea.com/pc/css/admin/global.css" type="text/css" media="screen" />
		
		<link type="text/css" rel="stylesheet" href="http://st.midea.com/pc/css/admin/global.css" />
		<link type="text/css" rel="stylesheet" href="http://st.midea.com/pc/css/admin/task.css" />

	</head>
	<body class="">
		<!--头部-->
		<div class="header">
			<div class="container">
				<h1 class="title">
					<a>管理系统<img src="http://static.midea.com/pc/img/admin/logo.png" /></a>
				</h1><div class="top_menu" name="username">
					<ul>
						<li>
							<p>欢迎您</p>
						</li>
						<li>
							<a>退出</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="wrap">
			<div class="container">
				<!--侧栏-->
				<div class="side_bar">
<div class="nav_menu" id="" name="userMenu">
<h3>一级菜单</h3>
<ul>
<li class="item item_unfold">
<a class="item_link" href="javascript::void(0)">二级菜单</a>
<ul class="sub_menu">
<li class="sub_item">
<a href="<?php echo site_url('goods/index');?>" class="sub_item_link" id="publishProduct">SKU列表</a>
</li>
<li class="sub_item">
<a href="<?php echo site_url('goods/edit');?>" class="sub_item_link" id="">添加SKU</a>
</li>
</ul>
</li>
</ul>
<h3>一级菜单</h3>
<ul>
<li class="item item_unfold">
<a class="item_link" href="javascript::void(0)">二级菜单</a>
<ul class="sub_menu">
<li class="sub_item">
<a href="" class="sub_item_link" id="publishProduct">三级菜单</a>
</li>
<li class="sub_item">
<a href="" class="sub_item_link" id="">三级菜单</a>
</li>
</ul>
</li>
</ul>
</div>
</div>

				<div class="content">
					
	<div class="mod_page_title">
		<h3>编辑商品详情</h3>
	</div>
	<div id="divSkuBasicBar" class="mod_common_hd">
		<h3>填写商品信息</h3>
	</div>
	<div id="divSkuBasicArea" class="mod_common_bd">
		<div class="mod_form goods_info_input_form ziyin_info_input_form">
			<div class="title_row">
				<h4>SKU基本信息</h4>
			</div>
			<form id="divSkuBasicInfoShowArea" class="mod_form goods_info_input_form">
				<div class="row">
					<div class="label_col">
						<span class="required">* </span>
						<label>产品编码</label>
					</div>
					<div class="con_col">
						<input class="txt_long" name="strItemCode" required="required" id="strItemCode" value="<?php if(isset($strItemCode)) echo htmlspecialchars($strItemCode);?>" />
					</div>
				</div>
				<div class="row">
					<div class="label_col">
						<span class="required">* </span>
						<label>型号</label>
					</div>
					<div class="con_col">
						<input class="txt_long" name="strModelNum" required="required" id="strModelNum" value="<?php if(isset($strModelNum)) echo htmlspecialchars($strModelNum);?>" />
					</div>
				</div>
				<div class="row">
					<div class="label_col">
						<span class="required">* </span>
						<label>条型码</label>
					</div>
					<div class="con_col">
						<input class="txt_long" name="strBarCode" required="required" id="strBarCode" value="<?php if(isset($strBarCode)) echo htmlspecialchars($strBarCode);?>" />
					</div>
				</div>
				<div class="row">
					<div class="label_col">
						<span class="required">* </span>
						<label>品牌</label>
					</div>
					<div class="con_col">
						<?php $brandOption = getSkuBrandOption();?>
						<select class="select_wrap" name="lBrandId" required="required"><?php foreach($brandOption as $key=>$val){?><option value="<?php echo htmlspecialchars($key);?>"><?php echo htmlspecialchars($val);?></option><?php }?></select>
					</div>
				</div>
				<div class="row">
					<div class="label_col">
						<span class="required">* </span>
						<label>产品标题</label>
					</div>
					<div class="con_col">
						<input class="txt_long" name="strSkuTitle" required="required" id="strSkuTitle" value="<?php if(isset($strSkuTitle)) echo htmlspecialchars($strSkuTitle);?>" />
					</div>
				</div>
				<div class="row">
					<div class="label_col">
						<label>标题简称</label>
					</div>
					<div class="con_col">
						<input class="txt_long" name="strBriefTitle" id="strBriefTitle" value="<?php if(isset($strBriefTitle)) echo htmlspecialchars($strBriefTitle);?>" />
					</div>
				</div>
				<div class="row">
					<div class="label_col">
						<label>副题标</label>
					</div>
					<div class="con_col">
						<input class="txt_long" name="strSkuSubTitle" id="strSkuSubTitle" value="<?php if(isset($strSkuSubTitle)) echo htmlspecialchars($strSkuSubTitle);?>" />
					</div>
				</div>
				<div class="row">
					<div class="label_col">
						<span class="required">* </span>
						<label>参考价</label>
					</div>
					<div class="con_col">
						<input class="txt_long" name="lSkuReferPrice" required="required" id="lSkuReferPrice" value="<?php if(isset($lSkuReferPrice)) echo fen2yuan($lSkuReferPrice);?>" />
					</div>
				</div>
				<div class="row">
					<div class="label_col">
						<span class="required">* </span>
						<label>成本价</label>
					</div>
					<div class="con_col">
						<input class="txt_long" name="lCostPrice" required="required" id="lCostPrice" value="<?php if(isset($lCostPrice)) echo fen2yuan($lCostPrice);?>" />
					</div>
				</div>
				<div class="row">
					<div class="label_col">
						<span class="required">* </span>
						<label>商品状态</label>
					</div>
					<div class="con_col">
						<?php $skuStateOption = getSkuStateOption();?>
						<?php $__CONT__=array();foreach($skuStateOption as $key=>$val) { ?><?php if(isset($skuStateOption)&&($skuStateOption==$key||(is_array($skuStateOption)&&in_array($key,$skuStateOption)))){ ?><?php $__CONT__[]='<input class="select_wrap" first="请选择" name="lSkuState" type="radio" checked="checked" value="'.htmlspecialchars($key).'" />'.htmlspecialchars($val);?><?php }else{?><?php $__CONT__[]='<input  class="select_wrap" first="请选择" name="lSkuState" type="radio" value="'.htmlspecialchars($key).'" />'.htmlspecialchars($val);?><?php }?><?php }?><?php echo implode('',$__CONT__);?>
					</div>
				</div>
				<div class="row">
					<div class="label_col">
						<label>是否是配件</label>
					</div>
					<div class="con_col">
						<?php $skuAccessoryOption = getSkuAccessoryOption();?>
						<?php $__CONT__=array();foreach($skuAccessoryOption as $key=>$val) { ?><?php if(isset($nIsAccessory)&&($nIsAccessory==$key||(is_array($nIsAccessory)&&in_array($key,$nIsAccessory)))){ ?><?php $__CONT__[]='<input class="select_wrap" first="请选择" name="nIsAccessory" type="radio" checked="checked" value="'.htmlspecialchars($key).'" />'.htmlspecialchars($val);?><?php }else{?><?php $__CONT__[]='<input  class="select_wrap" first="请选择" name="nIsAccessory" type="radio" value="'.htmlspecialchars($key).'" />'.htmlspecialchars($val);?><?php }?><?php }?><?php echo implode('',$__CONT__);?>
					</div>
				</div>
				<div class="row">
					<div class="label_col">
						<label>是否需要安装</label>
					</div>
					<div class="con_col">
						<?php $yesno = getYesNo();?>
						<?php $__CONT__=array();foreach($yesno as $key=>$val) { ?><?php if(isset($nNeedInstall)&&($nNeedInstall==$key||(is_array($nNeedInstall)&&in_array($key,$nNeedInstall)))){ ?><?php $__CONT__[]='<input class="select_wrap" first="请选择" name="nNeedInstall" type="radio" checked="checked" value="'.htmlspecialchars($key).'" />'.htmlspecialchars($val);?><?php }else{?><?php $__CONT__[]='<input  class="select_wrap" first="请选择" name="nNeedInstall" type="radio" value="'.htmlspecialchars($key).'" />'.htmlspecialchars($val);?><?php }?><?php }?><?php echo implode('',$__CONT__);?>
					</div>
				</div>
				<div class="row">
					<div class="label_col">
						<label>上市时间</label>
					</div>
					<div class="con_col">
						<input class="txt_long" name="nMarketTime" id="nMarketTime" value="<?php if(isset($nMarketTime)) echo stamp2date($nMarketTime);?>" />
					</div>
				</div>
				<div class="title_row">
					<h4>包装信息</h4>
				</div>
				<div class="row">
					<div class="label_col">
						<label>净长</label>
					</div>
					<div class="con_col">
						<input class="txt_long" name="lNetLength" id="lNetLength" value="<?php if(isset($lNetLength)) echo htmlspecialchars($lNetLength);?>" />mm
					</div>
				</div>
				<div class="row">
					<div class="label_col">
						<label>净宽</label>
					</div>
					<div class="con_col">
						<input class="txt_long" name="lNetWidth" id="lNetWidth" value="<?php if(isset($lNetWidth)) echo htmlspecialchars($lNetWidth);?>" />mm
					</div>
				</div>
				<div class="row">
					<div class="label_col">
						<label>净高</label>
					</div>
					<div class="con_col">
						<input class="txt_long" name="lNetHeight" id="lNetHeight" value="<?php if(isset($lNetHeight)) echo htmlspecialchars($lNetHeight);?>" />mm
					</div>
				</div>
				<div class="row">
					<div class="label_col">
						<label>净体积</label>
					</div>
					<div class="con_col">
						<input class="txt_long" name="lNetVolume" id="lNetVolume" value="<?php if(isset($lNetVolume)) echo htmlspecialchars($lNetVolume);?>" />mm<sup>3</sup>
					</div>
				</div>
				<div class="row">
					<div class="label_col">
						<label>净重</label>
					</div>
					<div class="con_col">
						<input class="txt_long" name="lNetWidth" id="lNetWidth" value="<?php if(isset($lNetWidth)) echo div1000($lNetWidth);?>" />Kg
					</div>
				</div>
				<div class="row">
					<div class="label_col">
						<label>长（带包装）</label>
					</div>
					<div class="con_col">
						<input class="txt_long" name="lPkgLength" id="lPkgLength" value="<?php if(isset($lPkgLength)) echo htmlspecialchars($lPkgLength);?>" />mm
					</div>
				</div>
				<div class="row">
					<div class="label_col">
						<label>宽（带包装）</label>
					</div>
					<div class="con_col">
						<input class="txt_long" name="lPkgWidth" id="lPkgWidth" value="<?php if(isset($lPkgWidth)) echo htmlspecialchars($lPkgWidth);?>" />mm
					</div>
				</div>
				<div class="row">
					<div class="label_col">
						<label>高（带包装）</label>
					</div>
					<div class="con_col">
						<input class="txt_long" name="lPkgHeight" id="lPkgHeight" value="<?php if(isset($lPkgHeight)) echo htmlspecialchars($lPkgHeight);?>" />mm
					</div>
				</div>
				<div class="row">
					<div class="label_col">
						<label>体积（带包装）</label>
					</div>
					<div class="con_col">
						<input class="txt_long" name="lPkgVolume" id="lPkgVolume" value="<?php if(isset($lPkgVolume)) echo htmlspecialchars($lPkgVolume);?>" />mm<sup>3</sup>
					</div>
				</div>
				<div class="row">
					<div class="label_col">
						<label>重量（带包装）</label>
					</div>
					<div class="con_col">
						<input class="txt_long" name="lPkgWeight" id="lPkgWeight" value="<?php if(isset($lPkgWeight)) echo div1000($lPkgWeight);?>" />Kg
					</div>
				</div>
				<div class="row">
					<div class="label_col">
						<label>  </label>
					</div>
					<div class="con_col">
						<button id="saveSkuInfo" class="mod_btn" type="submit">保存商品基本信息</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div id="divSkuPicBar" class="mod_common_hd"<?php if(!$skuId){ ?> style="display:none"<?php } ?>>
		<h3>上传图片</h3>
	</div>
	<div id="divSkuPicArea" class="mod_common_bd"<?php if(!$skuId){ ?> style="display:none"<?php } ?>>
		<div class="ziying_pics_upload">
			<div class="task_edit_ulist">
				<ul id="pic-list">
					<?php foreach($mainPicLists as $k=>$v){?>
						<?php if($k>9) continue;?>
						<li>
							<?php 
								$_imgsrc='';
								if(!empty($v->strLocalPicRelativePath)){
									$_imgsrc = image2url($v->strLocalPicRelativePath."/".$v->lPicIndex.'.'.imageType2ext($v->lPicType)).'?'.$v->lLastModifyTime;
								}
							?>
							<img class="uploadMainPicImg" src="<?php echo $_imgsrc;?>" data-index="<?php echo $k;?>" width="60" height="60" />
							<button class="uploadMainPicBtn" data-index="<?php echo $k;?>">上传图片</button>
							<button class="delMainPic" data-index="<?php echo $k;?>">删除图片</button>
						</li>
					<?php }?>
				</ul>
			</div>
		</div>
	</div>
	<div id="divSkuDetailBar" class="mod_common_hd"<?php if(!$skuId){ ?> style="display:none"<?php } ?>>
		<h3>编辑商品详情</h3>
		<span class="mod_common_hd_tips"></span>
	</div>
	<div id="divDetailBar"<?php if(!$skuId){ ?> style="display:none"<?php } ?>>
			
	</div>

				</div>
			</div>
		</div>
		<div class="footer">
			<div class="container copyrights">© 1998 - <span id="">2013</span> 版权所有</div>
		</div>
		
	<?php 
		$skuStr='?';
		if($skuId){
			$skuStr .= 'skuId='.$skuId;
		}
		$json = array(
			'detailModule'=>array(
				'saveUrl'=>site_url('goods/saveDetail').$skuStr,
				'upload_url'=>site_url('picture/detailPic').$skuStr,
				'flash_url'=>base_url('/static/swf/swfupload.swf'),
				'previewUrl'=>site_url('goods/previewpc').$skuStr,
			),
			'mainPic' => array(
				'uploadUrl'=>site_url('picture/mainPic').$skuStr,
				'delUrl'=>site_url('picture/delMainPic').$skuStr,
				'flash_url'=>base_url('/static/swf/swfupload.swf')
			)
		);
		if(!empty($detail)) $json['detailModule']['data'] = json_decode($detail);
		if($skuId) $json['skuId'] = $skuId;
	?>
	<script type="text/javascript">
		__initData = <?php echo json_encode($json);?>;</script>
	<?php echo ssinclude("/sinclude/jsi/md.ic.goodspublish.js.html");?>


	</body>
</html>
