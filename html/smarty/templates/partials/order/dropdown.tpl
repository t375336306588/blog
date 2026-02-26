<div class="dropdown">
    <a class="btn btn-sm btn-secondary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        {foreach $order->getlinks() as $data}
            {if $data.active}
                {$data.label}
            {/if}
        {/foreach}
    </a>

    <ul class="dropdown-menu">
        {foreach $order->getlinks() as $data}
            <li>
                <a class="dropdown-item {if $data.active}active{/if}"
                   href="{$data.link|default:'#'}"
                >{$data.label}</a>
            </li>
        {/foreach}
    </ul>
</div>
