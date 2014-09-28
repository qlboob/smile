<include include>
testvar:{$testvar}<hr>
<if 1>
t
<elseif 1 neq 3 />
fasdf
</if>
<if 1>
t

<switch testvar>
<case 1>t</case>
<default/>
default
</switch>

<elseif 1 neq 3 />
fasdf
</if>

<literal>
<if 3>
goood
</if>
</literal>
{$testvar|yesno}
<switch testvar>
<case 1>t</case>
<default/>
default
</switch>
<hr />
<foreach name=data>
{$v|}
</foreach>

<hr />iterate<br />
<iterate name=data>
{$v|}
</iterate>

<hr />htmlcache <br />
<htmlcache include />
<hr />
php<br />
<php>
echo 'yes';
</php>