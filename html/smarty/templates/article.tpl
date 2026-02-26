{extends file="layouts/app.tpl"}

{block name="content"}

    <h1 class="text-center fw-bold mb-4">#{$article->getId()} {$article->getTitle()}</h1>

    <h3>Description</h3>
    <p class="mb-4">{$article->getDescription()}</p>

    <h3>Content</h3>
    <p class="mb-4">{$article->getContent()}</p>

    <h3>Similar</h3>
    <section class="category-section">
        <div class="row">
            {foreach $article->getSimilarArticles() as $article}
                {include file="partials/article/preview.tpl" article=$article}
            {foreachelse}
                <div class="col-12">
                    <p class="text-muted">No articles!</p>
                </div>
            {/foreach}
        </div>
    </section>

{/block}
