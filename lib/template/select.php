<taglib form/>
<style type="text/css">

/* test info */
.test{
	
	color: red;
}

</style>
<form:select name=opt options=opt />
<form:select name=www options=opt cache=1 first=select selected=g />
<form:select name=www optionsfile=data/no cache=1 first=select selected=g />
<form:select name=www valuesfile=data/no cache=1 first=select selected=g />