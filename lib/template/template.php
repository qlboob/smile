<taglib form />
<html>
<head>
<title>{$title|}</title>
<script type="text/javascript" src="syntaxhighlighter/scripts/shCore.js"></script>
<script type="text/javascript"
	src="syntaxhighlighter/scripts/shBrushPhp.js"></script>
<script type="text/javascript"
	src="syntaxhighlighter/scripts/shBrushXml.js"></script>
<link type="text/css" rel="stylesheet"
	href="syntaxhighlighter/styles/shCore.css" />
<link type="text/css" rel="stylesheet"
	href="syntaxhighlighter/styles/shThemeDefault.css" />
<link href="../../demo/css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
	SyntaxHighlighter.config.clipboardSwf = 'syntaxhighlighter/scripts/clipboard.swf';
	SyntaxHighlighter.all();
</script>
</head>
<body>
<?php
$menu	=	array(
	array(
		'node'=>'Example',
		'children'=>array(
			array(
				'node'=>'Php',
				'href'=>'template.php?t=php',
			),
			array(
				'node'=>'Var',
				'href'=>'template.php?t=var',
			),
			array(
				'node'=>'If-else-elseif',
				'href'=>'template.php?t=if',
			),
			array(
				'node'=>'Switch',
				'href'=>'template.php?t=switch',
			),
			array(
				'node'=>'Present',
				'href'=>'template.php?t=present',
			),
			array(
				'node'=>'Empty',
				'href'=>'template.php?t=empty',
			),
			array(
				'node'=>'Defined',
				'href'=>'template.php?t=defined',
			),
			array(
				'node'=>'Iterate',
				'href'=>'template.php?t=iterate',
			),
			array(
				'node'=>'Foreach',
				'href'=>'template.php?t=foreach',
			),
			array(
				'node'=>'include',
				'href'=>'template.php?t=include',
			),
			array(
				'node'=>'funDependent',
				'href'=>'template.php?t=funDependent',
			),
			array(
				'node'=>'literal',
				'href'=>'template.php?t=literal',
			),
			array(
				'node'=>'htmlcache',
				'href'=>'template.php?t=htmlcache',
			),
			array(
				'node'=>'nohtmlcache',
				'href'=>'template.php?t=nohtmlcache',
			),
			/*array(
				'node'=>'assign',
				'href'=>'template.php?t=assign',
			),*/
		),
	),
	array(
		'node'=>'Form',
		'children'=>array(
			array(
				'node'=>'input',
				'href'=>'template.php?t=input',
			),
			array(
				'node'=>'field',
				'href'=>'template.php?t=field',
			),
			array(
				'node'=>'hidden',
				'href'=>'template.php?t=hidden',
			),
			array(
				'node'=>'presenthidden',
				'href'=>'template.php?t=presenthidden',
			),
			array(
				'node'=>'textarea',
				'href'=>'template.php?t=textarea',
			),
			array(
				'node'=>'checkbox',
				'href'=>'template.php?t=checkbox',
			),
			array(
				'node'=>'checkboxs',
				'href'=>'template.php?t=checkboxs',
			),
			array(
				'node'=>'select',
				'href'=>'template.php?t=select',
			),
			array(
				'node'=>'radios',
				'href'=>'template.php?t=radios',
			),
		)
	),
	array(
		'node'=>'Sortable',
		'children'=>array(
			array(
				'node'=>'list',
				'href'=>'template.php?t=sortable',
			),
		),
	),
	array(
		'node'=>'Clear Cache',
		'href'=>'clear.php',
	),
);
?>

<div id="left"><nohtmlcache>
<form method="post">
<fieldset><legend>config</legend>
<table width="100%">
	<tr>
		<td>compress Html</td>
		<td><form:checkbox name="compressHtml" value="1" /></td>
	</tr>
	<tr>
		<td>compress Php</td>
		<td><form:checkbox name="compressPhp" value="1" /></td>
	</tr>
	<tr>
		<td>exe js file</td>
		<td><form:checkbox name="exeJs" value="1" /></td>
	</tr>
	<tr>
		<td>compress js</td>
		<td><form:checkbox name="compressJs" value="1" /></td>
	</tr>
	<tr>
		<td>compress js file</td>
		<td><form:checkbox name="compressJsFile" value="1" /></td>
	</tr>
	<tr>
		<td>exe css</td>
		<td><form:checkbox name="exeCss" value="1" /></td>
	</tr>
	<tr>
		<td>compress css</td>
		<td><form:checkbox name="compressCss" value="1" /></td>
	</tr>
	<tr>
		<td>compress css file</td>
		<td><form:checkbox name=compressCssFile value=1 /></td>
	</tr>
	<tr>
		<td>html Cache</td>
		<td><form:checkbox name=htmlCache value=1 /></td>
	</tr>
	<tr>
		<td>template Cache</td>
		<td><form:checkbox name=cacheOn value=1 /></td>
	</tr>
	<tr>
		<td>Cacheid</td>
		<td><form:field name=cacheid size=4 /></td>
	</tr>
	<tr>
		<td>cacheTime</td>
		<td><form:field name=cacheTime size=4 /></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" value="update" /><input
			type="hidden" name="delcache" value="1" /></td>
	</tr>
</table>
</fieldset>
</form>
<form action="delcache.php" method="post" target="delcacheiframe">
<fieldset><legend>Delete Cache</legend>
<table width="100%">
	<tr>
		<td>template</td>
		<td><form:field name=template size=6 /></td>
	</tr>
	<tr>
		<td>cacheid</td>
		<td><form:field name=cacheid size=6 /></td>
	</tr>
</table>
<input type="submit" value="Delete" /></fieldset>
</form>
<iframe name="delcacheiframe" frameborder="0" width="0" height="0"></iframe>
</nohtmlcache> {:ul($menu)}</div>
<div class="right">

<div>template file: <pre class="brush: xml;">
{$tplFile|file_get_contents|}
</pre> cache File: <pre class="brush: php;">
{$cacheFile|file_get_contents|}
</pre></div>
</div>
</body>
</html>