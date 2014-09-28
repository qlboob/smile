<htmlcache>
<php>
function test($g){
return strtoupper($g);
}
</php>
	{$g|test}
	{:date('Y-m-d H:i:s')}
</htmlcache>
<htmlcache t />