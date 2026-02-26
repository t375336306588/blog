<article class="col-md-4 mb-4 preview-article">
    <div class="card h-100 shadow-sm">

        <div class="image" style="background: url('{$article->getImage()}') no-repeat center center / cover"></div>

        <div class="card-body">
            <h5 class="card-title text-truncate">#{$article->getId()} {$article->getTitle()}</h5>
            <p class="card-text text-muted small text-truncate">{$article->getDescription()|truncate:100}</p>
            <p class="text-end"><a href="{$article->getLink()}" class="btn btn-md btn-danger">Перейти</a></p>
        </div>
        <div class="card-footer bg-white border-0">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-secondary">{$article->getCreatedAt()}</small>
                <span class="badge bg-light text-dark border">👁 {$article->getViews()}</span>
            </div>
        </div>
    </div>
</article>