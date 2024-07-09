<div class="form-container mb-4">
    <form action="" method="post" class="row g-3">
        <div class="col-md-6">
            <label for="arrived_at" class="form-label">Arrived At</label>
            <input type="datetime-local" id="arrived_at" name="arrived_at" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="leaved_at" class="form-label">Leaved At</label>
            <input type="datetime-local" id="leaved_at" name="leaved_at" class="form-control" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>

<?php
$tracker->fetchRecords($page);
?>

<!-- Pagination -->
<nav aria-label="Page navigation example">
    <ul class="pagination">
        <li class="page-item <?php if ($page <= 1) {
                                    echo 'disabled';
                                } ?>">
            <a class="page-link" href="<?php if ($page > 1) {
                                            echo '?page=' . ($page - 1);
                                        } else {
                                            echo '#';
                                        } ?>">Previous</a>
        </li>
        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
            <li class="page-item <?php if ($page == $i) {
                                        echo 'active';
                                    } ?>">
                <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?php if ($page >= $total_pages) {
                                    echo 'disabled';
                                } ?>">
            <a class="page-link" href="<?php if ($page < $total_pages) {
                                            echo '?page=' . ($page + 1);
                                        } else {
                                            echo '#';
                                        } ?>">Next</a>
        </li>
    </ul>
</nav>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to mark this record as worked off?
            </div>
            <div class="modal-footer">
                <form action="" method="post">
                    <input type="hidden" name="worked_off" id="workedOffId">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" name="update">Yes</button>
                </form>
            </div>
        </div>
    </div>
</div>