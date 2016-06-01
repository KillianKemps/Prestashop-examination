<p>
{foreach from=$look item=child name=look}
{$child->name}<br/>
{$child->price}<br/>
{/foreach}
{foreach from=$pack item=child name=look}
{$child}
{/foreach}
</p>
