<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>预览</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="http://st.midea.com/pc/css/admin/gb.css?t=20130924141311" type="text/css" />
		<style type="text/css">
			.xdetails{width:750px;margin:0 auto;}
			.xdetails_floor_bd:after {content: "";visibility: hidden;height: 0;line-height: 0;display: block;clear: both;}
			.xdetails_floor {padding-top: 35px;width: 100%;margin: 0 auto;}
			.xdetails_floor_hd {position: relative;height: 20px; border-bottom: 2px solid #d7d7d7;font: normal 14px/18px '\5FAE\8F6F\96C5\9ED1', SimSun;color: #666666;font-weight: normal;}
			.xdetails_floor_tit {position:relative;float:left;margin-right:10px;border-bottom:#2457A6 solid 2px;padding-bottom:2px;font: normal 18px/18px '\5FAE\8F6F\96C5\9ED1', SimHei;font-family: SimHei, SimSun;color: #2457a6;padding-right: 10px;vertical-align: middle;}
			.xdetails_floor_bd {padding: 20px 0 0 0; zoom: 1;}
			.xdetails_cnt:after {content: "";visibility: hidden;height: 0;line-height: 0;display: block;clear: both;}
			.xdetails_cnt1 .xdetails_cnt_col1,
			.xdetails_cnt1 .xdetails_cnt_col2{float:left;}
			.xdetails_cnt1 .xdetails_cnt_col2{width:388px;margin-left:10px;_display:inline;}
			.xdetails_cnt1 .xdetails_cnt_properties th,
			.xdetails_cnt1 .xdetails_cnt_properties td{padding:10px;border:#fff solid 1px;}
			.xdetails_cnt1 .xdetails_cnt_properties th{background-color:#E1E1E1;white-space:nowrap;}
			.xdetails_cnt1 .xdetails_cnt_properties td{background-color:#F5F5F5;}
			.xdetails_cnt2 .xdetails_cnt_col1,
			.xdetails_cnt2 .xdetails_cnt_col2{float:left;}
			.xdetails_cnt2 .xdetails_cnt_col1{width:185px;}
			.xdetails_cnt2 .xdetails_cnt_col2{width:555px;margin-left:10px; _display:inline;}
			.xdetails_cnt3 .xdetails_cnt_col1{float:right; width:185px;}
			.xdetails_cnt3 .xdetails_cnt_col2{float:right; width:555px;margin-left:10px; _display:inline;}
			.xdetails_cnt4 .xdetails_cnt_col1{float:left; width:350px;}
			.xdetails_cnt4 .xdetails_cnt_col2{float:right; width:350px;}
			.xdetails_cnt{padding:20px 0; clear:both;}
			.xdetails_cnt img{max-width:750px;}
			.xdetails_cnt_tit{font-size:16px;font-family:microsoft yahei;word-break:break-all;}
			.xdetails_cnt_txt{margin:10px 0; font-size:12px; line-height:20px;}
			.xdetails_tit{width:750px;height:50px;margin-top:10px; background:url(http://static.gtimg.com/icson/img/detail/v2/xdetails_title_bg.png) no-repeat;}
			.xdetails_tit h4{ line-height:50px; font-size:24px;font-weight:400; font-family:microsoft yahei;color:#fff;}
			.xdetails_tit1{background-position:0 0;}
			.xdetails_tit1 h4,
			.xdetails_tit3 h4{text-align:center;}
			.xdetails_tit3 h4{text-indent:-34px;}
			.xdetails_tit2{background-position:0 -50px;}
			.xdetails_tit2 h4{line-height:42px; text-indent:35px}
			.xdetails_tit3{background-position:0 -100px;}</style>
	</head>
	<body>
		<div class="xdetails">
			<?php $groupInfo = array(
				13=>'产品介绍',
				14=>'规格参数',
				15=>'服务支持',
				16=>'相关问题',
				);?>
			<?php foreach($lists as $groupId=>$templatesStr){?>
				<?php $templates = json_decode($templatesStr);?>
				<div class="xdetails_floor xdetails_intro">
					<div class="xdetails_floor_hd">
						<h3 class="xdetails_floor_tit"><?php echo $groupInfo[$groupId];?></h3>
					</div>
					<div class="xdetails_floor_bd">
						<?php foreach($templates as $k=>$template){?>
							<?php switch($template->templateId){
								 case 1:?>
									<div class="xdetails_cnt xdetails_cnt1">
										<div class="xdetails_cnt_col1">
											<img src="<?php echo $template->data->picUrl;?>" width="300" />
										</div>
										<div class="xdetails_cnt_col2">
											<table class="xdetails_cnt_properties">
												<tbody>
													<?php foreach($template->data->list as $k=>$row){?>
														<tr>
															<th><?php echo htmlspecialchars($row->name);?></th>
															<td><?php echo htmlspecialchars($row->value);?></td>
														</tr>
													<?php }?>
												</tbody>
											</table>
										</div>
									</div>
								<?php break; ?>
								<?php case 2:?>
									

<div class="xdetails_tit xdetails_tit2">
	<h4><?php echo htmlspecialchars($template->data->desc);?>
</h4>
</div>
								<?php break; ?>
								<?php case 4:?>
									

<div class="xdetails_cnt">
	<img src="<?php echo $template->data->picUrl;?>" />

</div>

								<?php break; ?>
							<?php }?>
						<?php }?>
					</div>
				</div>
			<?php }?>
		</div>
	</body>
</html>