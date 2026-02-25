{extends file="layouts/app.tpl"}

{block name="content"}
    <h1 class="text-center display-1 fw-bold">500 | Server error</h1>
    <p class="text-center">File: {$error->getFile()}</p>
    <p class="text-center">String: {$error->getLine()}</p>
    <p class="text-center">Message: {$error->getMessage()}</p>
    <p class="text-center">Code: {$error->getCode()}</p>
{/block}