{extends file="layouts/app.tpl"}

{block name="content"}
    <h1 class="text-center fw-bold mb-4">Home</h1>

    {foreach $categories as $category}
        <section class="category-section mb-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 mb-0">#{$category->getId()} {$category->getTitle()}</h2>
                <a href="{$category->getLink()}" class="btn btn-outline-primary btn-sm">More</a>
            </div>

            <div class="row">
                {foreach $category->getLatestArticles(3) as $article}
                    {include file="partials/article/preview.tpl" article=$article}
                {foreachelse}
                    <div class="col-12">
                        <p class="text-muted">No articles!</p>
                    </div>
                {/foreach}
            </div>

        </section>
    {/foreach}

{/block}
