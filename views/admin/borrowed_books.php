<?php
require_once('../layouts/header.php');
require_once __DIR__ . '/../../models/Borrowed_books.php';
require_once __DIR__ . "/../../models/Users.php";

// Initialize Borrowed Books Model
// Initialize Borrowed Books Model
$Borrowed_BooksModel = new Borrowed_Books();
$user_dropdown = $Borrowed_BooksModel->user_dropdown();
$book_dropdown = $Borrowed_BooksModel->book_dropdown();
// $pendingRequests = $Borrowed_BooksModel->getPendingRequests();

// Fetch borrowed books based on user role
if ($Role === 'admin') {
    // Admins can view all borrowed books
    $borrowedBooks = $Borrowed_BooksModel->getAllWithBookAndMember();
} else {
    // Members can only view their own borrowed books
    $borrowedBooks = $Borrowed_BooksModel->getAllWithBookAndUserByUserId($userId);
}
// Debugging: Ensure User ID is correctly fetched


?>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Dashboard /</span> Borrowed Books
        <button type="button" class="btn btn-dark float-end" data-bs-toggle="modal" data-bs-target="#add_borrowed_books">
            <i class="bx bx-plus-medical "></i>
        </button> 
    </h4>
        <br>  <!-- Borrowed Books Table -->
    <div class="card-body p-0 table-responsive">
        <table class="table table-striped mb-4">
            <thead>
                <tr>
                    <?php if ($Role === 'admin') : ?>
                        <th>Actions</th>
                    <?php endif; ?>
                    <th class="text-nowrap">User ID</th>
                    <th class="text-nowrap">User Name</th>
                    <th class="text-nowrap">Book ID</th>
                    <th class="text-nowrap">Book Name</th>
                    <th class="text-nowrap">Borrow Status</th>
                    <th class="text-nowrap">Borrowed At</th>
                    <th class="text-nowrap">Due Date</th>
                    <th class="text-nowrap">Returned At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($borrowedBooks)) : ?>
                    <?php foreach ($borrowedBooks as $book) : ?>
                        <tr>
                            <?php if ($Role === 'admin') : ?>
                                <td>
                                    <a class="btn btn-sm btn-outline-dark m-2" 
                                       href="<?= url('views/admin/edit_borrowed.php?id=' . $book['ID'] ?? '') ?>">
                                        <i class="bx bx-edit btn-outline-dark"></i>
                                    </a>
                                </td>
                            <?php endif; ?>
                            <td class="text-nowrap"><?= $book['UserID'] ?? ''; ?></td>
                            <td class="text-nowrap"><?= $book['user_name'] ?? ''; ?></td>
                            <td class="text-nowrap"><?= $book['BookID'] ?? ''; ?></td>
                            <td class="text-nowrap"><?= $book['book_name'] ?? ''; ?></td>
                            <td class="text-nowrap"><?= $book['BorrowStatus'] ?? ''; ?></td>
                            <td class="text-nowrap"><?= $book['BorrowDate'] ?? ''; ?></td>
                            <td class="text-nowrap"><?= $book['DueDate'] ?? ''; ?></td>
                            <td class="text-nowrap"><?= $book['ReturnDate'] ?? ''; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="9" class="text-center">No borrowed books found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
    <!-- add borrowed books -->
<div class="modal fade" id="add_borrowed_books" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="create-form" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Add Borrowed Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_borrowed">
                    <div class="row">
                        <div class="mb-3 col-6">
                            <label for="user_id" class="form-label">User Id:</label>
                            <div class="dropdown-container">
                                <!-- Search Input for User IDs -->
                                <input type="text" id="user-search" class="form-control" placeholder="Search User IDs..." autocomplete="off">

                                <!-- Dropdown List for User IDs -->
                                <ul id="user-list" class="dropdown-list">
                                    <li class="dropdown-item" data-value="">Select a User ID</li>
                                    <?php foreach ($user_dropdown as $user): ?>
                                        <li class="dropdown-item" data-value="<?= $user['ID'] ?>"><?= $user['ID'] ?> - <?= $user['Username'] ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <!-- Hidden Input to hold the selected User ID -->
                            <input type="hidden" id="user_id" name="user_id">
                        </div>
                        <div class="mb-3 col-6">
                            <label for="book_id" class="form-label">Book Id:</label>
                            <div class="dropdown-container">
                                <input type="text" id="book-search" class="form-control" placeholder="Search Book IDs..." autocomplete="off">
                                <ul id="book-list" class="dropdown-list">
                                    <li class="dropdown-item" data-value="">Select a Book ID</li>
                                    <?php foreach ($book_dropdown as $book): ?>
                                        <li class="dropdown-item" data-value="<?= $book['ID'] ?>"><?= $book['ID'] ?> - <?= $book['Title'] ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <input type="hidden" id="book_id" name="book_id">
                        </div>


                        <div class=" mb-3 col-6">
                            <label for="book_status" class="form-label">Borrow Status:</label>
                            <select class="form-select" id="borrow_status" aria-label="Default select example" name="borrow_status" required>
                                <option value="borrowed" class=" text-info ">Borrowed</option>
                                <option value="returned" class=" text-success ">Returned</option>
                                <option value="due_time_over" class=" text-danger ">Due Time Over</option>
                            </select>
                        </div>

                        <div class="mb-3 col-6">
                            <label for="html5-datetime-local-input" class="form-label">BorrowDate</label><br>
                            <input class="form-control" type="date" name="BorrowDate" />
                        </div>
                        <div class="mb-3 col-6">
                            <label for="html5-datetime-local-input" class="form-label">Due Date:</label><br>
                            <input class="form-control" type="date" name="due_date" />
                        </div>
                        <?php if ($Role === 'admin') : ?>
                        <div class="mb-3 col-6">
                            <label for="html5-datetime-local-input" class="form-label">Returned At:</label><br>
                            <input class="form-control" type="date" name="returned_at" />
                        </div>
                        <?php endif; ?>

                        <div class="mb-3 mt-3">
                            <div id="additional-fields">
                            </div>
                        </div>

                        <div class="mb-3 mt-3">
                            <div id="alert-container"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="button" class="btn btn-dark" id="create">Create</button>
                    </div>
            </form>
        </div>
    </div>
</div>
</div>

<?php require_once('../layouts/footer.php'); ?>
<script src="<?= asset('assets/forms-js/borrowed.js') ?>"></script>
