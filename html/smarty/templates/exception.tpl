{extends file="layouts/app.tpl"}

{block name="content"}
    <h1 class="text-center fw-bold mb-4">{$exception->getCode()} error</h1>
    <p class="text-center fw-bold">{$exception->getMessage()}</p>
    <p class="text-center text-muted">{$exception->getFile()}:{$exception->getLine()}</p>
{/block}