<?php 
	use Com\Qinjq\Block\SLayout;
	foreach(array('head','closure') as $v){
		$regionVar = '_region'.ucfirst($v);
		$$regionVar = SLayout::getContent($v);
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title><?php echo $htmlHeadTitle;?></title>
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
		<![endif]-->
		<?php echo $_regionHead;?>
		
	</head>
	<body class="skin-blue">
		<header class="header">
			<a class="logo" href="<?php echo U(MODULE_NAME.'/Index/index');?>">Smile Cms</a>
			<nav class="navbar navbar-static-top" role="navigation">
				<a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
			</nav>
		</header>
		<div class="wrapper row-offcanvas row-offcanvas-left">
			<aside class="left-side sidebar-offcanvas">
				<section class="sidebar">
					<div class="user-panel">
						<div class="pull-left image">
							<img src="<?php echo __ROOT__;?>/skin/adminlte/img/me.png" class="img-circle" alt="User Image" />
						</div>
						<div class="pull-left info">
							<p>Hello, Luke</p>
							<a href="#">
								<i class="fa fa-circle text-success"></i>
								Online
							</a>
						</div>
					</div>
					<ul class="sidebar-menu">
						<li>
							<a href="<?php echo U('Dev/Index/index');?>">
								<i class="fa fa-dashboard"></i>
								<span>首页</span>
							</a>
						</li>
						<li class="treeview active">
							<a href="#">
								<i class="fa fa-bar-chart-o"></i>
								<span>核心</span>
								<i class="fa fa-angle-left pull-right"></i>
							</a>
							<ul class="treeview-menu">
								<li>
									<a href="<?php echo U('Dev/Site/index');?>">
										<i class="fa fa-angle-double-right"></i>
										站点
									</a>
								</li>
								<li>
									<a href="<?php echo U('Dev/Model/index',array('sit_id'=>$_COOKIE['_sit_id']));?>">
										<i class="fa fa-angle-double-right"></i>
										模型
									</a>
								</li>
								<li>
									<a href="<?php echo U('Dev/Form/index',array('sit_id'=>$_COOKIE['_sit_id']));?>">
										<i class="fa fa-angle-double-right"></i>
										表单
									</a>
								</li>
								<li>
									<a href="<?php echo U('Dev/Block/index');?>">
										<i class="fa fa-angle-double-right"></i>
										区块
									</a>
								</li>
								<li>
									<a href="<?php echo U('Dev/Event/index');?>">
										<i class="fa fa-angle-double-right"></i>
										事件
									</a>
								</li>
							</ul>
						</li>
					</ul>
				</section>
			</aside>
			<aside class="right-side">
				<section class="content-header">
					<h1>首页
						<small>xxx</small></h1>
					<ol class="breadcrumb">
						<li>
							<a href="#">
								<i class="fa fa-dashboard"></i>
								Home
							</a>
						</li>
						<li class="active">Test</li>
					</ol>
				</section>
				<section class="content">
					
				</section>
			</aside>
		</div>
		
		<?php echo $_regionClosure;?>
		
	</body>
</html>