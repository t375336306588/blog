{extends file="layouts/app.tpl"}

{block name="content"}

    <div class="d-flex justify-content-end">
        <div class="">
            {include file="partials/order/dropdown.tpl" order=$category->getOrderBy()}
        </div>
        <div class="ms-3">
            {include file="partials/order/dropdown.tpl" order=$category->getOrderType()}
        </div>
    </div>

    <h1 class="text-center fw-bold mb-4">#{$category->getId()} {$category->getTitle()}</h1>

    <p class="mb-4">{$category->getDescription()}</p>

    <section class="category-section mb-4">
        <div class="row">
            {foreach $category->getArticles() as $article}
                {include file="partials/article/preview.tpl" article=$article}
            {foreachelse}
                <div class="col-12">
                    <p class="text-muted">No articles!</p>
                </div>
            {/foreach}
        </div>
    </section>


    {include file="partials/pagination/simple.tpl" pagination=$category->getPagination()}


{/block}
