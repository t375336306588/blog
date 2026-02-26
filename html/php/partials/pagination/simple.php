<nav>
    <ul class="pagination justify-content-center">
        <?php foreach ($this->getPages() as $page): ?>
            <?php if ($page['separator']): ?>
            <li class="page-item disabled"><span class="page-link"><?= $page['index'] ?></span></li>
            <?php else: ?>
            <li class="page-item <?= $page['current'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= $page["link"] ?>"><?= $page["index"] ?></a>
            </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</nav>