{extends file="layouts/app.tpl"}

{block name="content"}
    <h1 class="text-center display-1 fw-bold">{$category->getTitle()}</h1>

    <p>{$category->getDescription()}</p>

    <section class="category-section my-5">
        <div class="row">
            {foreach $category->getLatestArticles(50) as $post}
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <img src="{$post->getImage()}" class="card-img-top" alt="{$post->getTitle()}">
                        <div class="card-body">
                            <h5 class="card-title">{$post->getTitle()}</h5>
                            <p class="card-text text-muted small">{$post->getDescription()|truncate:100}</p>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <small class="text-secondary">{$post->getCreatedAt()}</small>
                        </div>
                    </div>
                </div>
            {foreachelse}
                <div class="col-12">
                    <p class="text-muted">В этой категории пока нет статей.</p>
                </div>
            {/foreach}
        </div>
    </section>

{/block}
