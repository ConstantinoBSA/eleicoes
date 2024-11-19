<div class="row">
    <div class="col-md-4">
        Mostrando de <?php echo ($pagination['current_page'] - 1) * $pagination['per_page'] + 1; ?> 
        até <?php echo min($pagination['current_page'] * $pagination['per_page'], $pagination['total_records']); ?> 
        de <?php echo $pagination['total_records']; ?> registros
    </div>
    <?php if ($pagination['total_pages'] > 1): ?>
    <div class="col-md-8">
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-end">
                <li class="page-item <?php echo $pagination['current_page'] == 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] - 1; ?>&search=<?php echo htmlspecialchars($search, ENT_QUOTES); ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($search, ENT_QUOTES); ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php echo $pagination['current_page'] == $pagination['total_pages'] ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] + 1; ?>&search=<?php echo htmlspecialchars($search, ENT_QUOTES); ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>
