<block n=block1 t=tt>
<q>It's 1 block</q><br />
</block>

<block n=block2 t=t>
<q>It's 2 block</q><br />
</block>

<block n="before1" t="t" before="-">
<q>It's before1 block</q><br />
<blockto tt/>
</block>

<block n="before2" t="t" before="-">
<q>It's before2 block</q><br />
</block>

some

<div style="background:red">
<blockto t/>
</div>

<block n="after1" t="t" after="-">
<q>It's after1 block</q><br />
</block> 

<block n="after2" t="t" after="-">
<q>It's after2 block</q><br />
</block> 

<block n="blockafter1" t="tt" after="block1">
<q>It's blockafter1 block</q><br />
</block> 

<block n="blockafter1after" t="tt" after="blockafter1">
<q>It's blockafter1after block</q><br />
</block> 