<nav>
    <ul class="pagination justify-content-center">
        {foreach $pagination->getPages() as $page}
            {if $page.separator}
                <li class="page-item disabled">
                    <span class="page-link">{$page.index}</span>
                </li>
            {else}
                <li class="page-item {if $page.current}active{/if}">
                    <a class="page-link" href="{$page.link|default:'#'}">{$page.index}</a>
                </li>
            {/if}
        {/foreach}
    </ul>
</nav>