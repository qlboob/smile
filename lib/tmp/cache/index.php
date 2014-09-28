

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta charset="utf-8" />
		<title>管理系统--</title>
		<link rel="stylesheet" href="http://st.midea.com/pc/css/admin/global.css" type="text/css" media="screen" />
		
		<link type="text/css" rel="stylesheet" href="http://st.midea.com/pc/css/admin/global.css" />
		<link type="text/css" rel="stylesheet" href="http://st.midea.com/pc/css/admin/publish.css" />

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
		<h3>SKU列表</h3>
	</div>
	<form class="mod_form" method="get">
		<div class="row">
			<div class="label_col">
				<label>产品编码</label>
			</div>
			<div class="con_col">
				<input class="txt_medium" name="strItemCode" id="strItemCode" value="<?php if(isset($strItemCode)) echo htmlspecialchars($strItemCode);?>" />
			</div>
		</div>
		<div class="row">
			<div class="label_col">
				<label>产品型号</label>
			</div>
			<div class="con_col">
				<input class="txt_medium" name="strModelNum" id="strModelNum" value="<?php if(isset($strModelNum)) echo htmlspecialchars($strModelNum);?>" />
			</div>
		</div>
		<div class="row">
			<div class="label_col">
				<label>产品状态</label>
			</div>
			<div class="con_col">
				<?php $skuStateOption = getSkuStateOption();?>
				<select name="lSkuState"><option value="">全部</option><?php foreach($skuStateOption as $key=>$val){?><?php if(isset($lSkuState)&&($lSkuState==$key||(is_array($lSkuState)&&in_array($key,$lSkuState)))){?><option selected="selected" value="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($val);?></option><?php }else{?><option value="<?php echo htmlspecialchars($key);?>"><?php echo htmlspecialchars($val);?></option><?php }?><?php }?></select>
			</div>
		</div>
		<div class="row">
			<div class="label_col">
				<label>是否是配件</label>
			</div>
			<div class="con_col">
				<?php $skuAccessoryOption = getSkuAccessoryOption()?>
				<select name="nIsAccessory"><option value="">全部</option><?php foreach($skuAccessoryOption as $key=>$val){?><?php if(isset($nIsAccessory)&&($nIsAccessory==$key||(is_array($nIsAccessory)&&in_array($key,$nIsAccessory)))){?><option selected="selected" value="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($val);?></option><?php }else{?><option value="<?php echo htmlspecialchars($key);?>"><?php echo htmlspecialchars($val);?></option><?php }?><?php }?></select>
			</div>
		</div>
		<div class="row">
			<div class="label_col">
				<label>修改人</label>
			</div>
			<div class="con_col">
				<input class="txt_medium" name="strLastModifyPeople" id="strLastModifyPeople" value="<?php if(isset($strLastModifyPeople)) echo htmlspecialchars($strLastModifyPeople);?>" />
			</div>
		</div>
		<div class="row">
			<div class="label_col">
				<label>审核状态</label>
			</div>
			<div class="con_col">
				<input class="txt_medium" name="lSkuExamState" id="lSkuExamState" value="<?php if(isset($lSkuExamState)) echo htmlspecialchars($lSkuExamState);?>" />
			</div>
		</div>
		<div class="row">
			<div class="label_col"> </div>
			<div class="con_col">
				<button class="mod_btn" type="submit">查询</button>
			</div>
		</div>
	</form>
	<div class="mod_common">
		<div class="mod_common_hd">
			<h3>SKU列表</h3>
		</div>
		<div class="mod_common_bd">
			<?php if(!empty($pages)){?>
				<div class="pages"><?php echo $pages;?></div>
			<?php }?>
			<div class="box_table_hd">
				<table class="table_common table_thd_bg">
					<colgroup>
						<col width="7%" />
						<col width="22%" />
						<col width="22%" />
						<col width="12%" />
						<col width="15%" />
						<col width="12%" />
						<col width="10%" />
					</colgroup>
					<thead>
						<tr>
							<th>序号</th>
							<th align="left">产品信息</th>
							<th align="left">产品关键属性</th>
							<th align="left">添加时间</th>
							<th align="left">最后编辑时间</th>
							<th align="left">产品状态</th>
							<th>操作</th>
						</tr>
					</thead>
				</table>
			</div>
			<div class="box_table_bd">
				<table class="table_common table_thd_bg">
					<colgroup>
						<col width="7%" />
						<col width="22%" />
						<col width="22%" />
						<col width="12%" />
						<col width="15%" />
						<col width="12%" />
						<col width="10%" />
					</colgroup>
					<tbody>
						<?php if(empty($lists)){?>
							<p>没有数据</p>
						<?php }else{ ?>
							<?php foreach($lists as $k=>$v){?>
								<tr>
									<td class="item_num" align="center">
										<input type="checkbox" value="<?php echo $v->lSkuId;?>" /><?php echo $v->lSkuId;?>
									</td>
									<td class="item_name"><?php echo htmlspecialchars($v->strSkuTitle);?></td>
									<td class="item_keyattr"></td>
									<td><?php echo date('Y-m-d H:i:s',$v->nAddTime);?></td>
									<td><?php echo date('Y-m-d H:i:s',$v->nLastModifyTime);?></td>
									<td><?php echo $v->lSkuExamState;?></td>
									<td class="item_opt">
										<a target="_blank" href="<?php echo site_url('goods/edit');?>?id=<?php echo $v->lSkuId;?>">编辑</a>
									</td>
								</tr>
							<?php }?>
						<?php }?>
					</tbody>
				</table>
			</div>
			<?php if(!empty($pages)){?>
				<div class="pages"><?php echo $pages;?>



</div>
			<?php }?>
		</div>
	</div>

				</div>
			</div>
		</div>
		<div class="footer">
			<div class="container copyrights">© 1998 - <span id="">2013</span> 版权所有</div>
		</div>
		

	</body>
</html>
