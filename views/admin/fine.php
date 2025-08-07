<?php
require_once('../layouts/header.php');
require_once __DIR__ . '/../../models/Borrowed_books.php';

$Borrowed_BooksModel = new Borrowed_Books();
if ($Role == 'admin') {
    // Fetch all borrowed books with fines for admin
    $finetable = $Borrowed_BooksModel->getBorrowedBooksWithFines();
} else {
    // Fetch borrowed books with fines for the logged-in user
    $finetable = $Borrowed_BooksModel->getBorrowedBooksWithFinesByUser($userId);
}

// print_r($finetable);

?>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Fines
    </h4>



    <!-- /.card-header -->
    <div class="card-body p-0 table-responsive">
        <table class="table table-striped mb-4">
            <thead>
                <tr>
                    <?php if ($Role == 'admin') : ?>
                        <th></th>
                    <?php endif; ?>
                    <!-- <th class="text-nowrap">Member Id</th> -->
                    <th class="text-nowrap">Member Name</th>
                    <th class="text-nowrap">Fine</th>
                    <th class="text-nowrap">Paid Status</th>
                    <th class="text-nowrap">Fine Status</th>
                    <th class="text-nowrap">Paid Date</th>

                </tr>
            </thead>
            <tbody>

                <?php
                if (isset($finetable)) {
                    foreach ($finetable as $ft) {
                ?>
             
                        <tr>
                            <?php if ($Role == 'admin') : ?>
                                <td>
                                    <div>
                                        <a class="btn btn-sm btn-outline-dark m-2" href="<?= url('views/admin/fine_status.php?id=' . $ft['borrow_id'] ?? '') ?>"><i class="bx bx-edit btn-outline-dark"></i></a>
                                    </div>
                                </td>
                            <?php endif; ?>
                            <!-- <td class="text-nowrap"> <?= $ft['user_id'] ?? ""; ?> </td> -->
                            <td class="text-nowrap"> <?= $ft['user_name'] ?? ""; ?> </td>
                            <td class="text-nowrap"> <?= $ft['FineAmount'] ?? ""; ?> </td>
                            <td class="text-nowrap"> <?= $ft['PaidStatus'] ?? ""; ?> </td>
                            <td class="text-nowrap"> <?= $ft['FineStatus'] ?? ""; ?> </td>
                            <td class="text-nowrap"> <?= $ft['PaidDate'] ?? ""; ?> </td>

                        </tr>
                <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- update fine status -->
<!-- <div class="modal fade" id="edit-payment-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="create-form" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Paid / Pending</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_payment">
                    <input type="hidden" id="payment_id" name="id" value="">
                    <div class="row ">

                        <div class="col mb-3">
                            <label for="user_id" class="form-label">user id</label>
                            <input required type="text" name="user_id" id="user_id" class="form-control" placeholder="Enter Quantity" />
                        </div>

                        <div class="mb-3">
                            <label for="exampleFormControlSelect1" class="form-label">fine status</label>
                            <select class="form-select" id="fine_status" aria-label="Default select example" name="fine_status" required>
                                <option value="paid">paid</option>
                                <option value="pending">pending</option>
                            </select>
                        </div>
                        <div class="col mb-3">
                            <label for="html5-datetime-local-input" class="col-md-2 col-form-label">Update_At</label><br>
                            <div class="col-md-12">
                                <input class="form-control" type="date" value="" id="html5-datetime-local-input" name="updated_at" />
                            </div>
                        </div>
                    </div>


                    <div class="mb-3 mt-3">
                        <div id="edit-alert-container"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" id="create">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div> -->

<?php require_once('../layouts/footer.php'); ?>