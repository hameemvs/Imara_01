<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/Books.php';

$booksModel = new Books();
$data = $booksModel->getAll();

?>

<!-- Content -->


<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Book-collection
        <!-- Button trigger modal -->
        <?php if ($Role == 'admin') : ?>
        <button type="button" class="btn btn-dark float-end" data-bs-toggle="modal" data-bs-target="#new-book-modal">
            <i class="bx bx-book-add "> add new book</i>
            <?php endif; ?>
        </button>
    </h4>
    <div class="row m-3">
        <div class="col-6">
            <div class="d-flex align-items-center m-3">
                <i class="bx bx-search  btn btn-outline-dark"></i>
                <input type="text" id="searchInput" class="form-control border-0 shadow-none" placeholder="Search Book Name  " aria-label="Search..." />
            </div>
        </div>
        <div class="col-2">
            <div class="form-group my-3">
                <button class="btn btn-outline-dark d-inline" id="clear">Clear</button>
            </div>
        </div>
    </div>
    <hr>
    <!-- Basic Bootstrap Table -->
    <div class="card-body p-0 table-responsive">
        <table class="table table-striped mb-4">
            <thead>
                <tr>
                <?php if ($Role == 'admin') : ?>
                    <th>Edit</th>
                    <?php endif; ?>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>CategoryID</th>
                    <th>ISBN</th>
                    <th>Quantity</th>
                    <th>Added_at</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($data as $key => $book) {
                ?>
                    <tr>
                        <?php if ($Role == 'admin') : ?>
                        <td>
                            <div>
                                <a class="btn btn-sm  btn-outline-dark m-2 edit-book-btn" data-bs-toggle="modal" data-bs-target="#edit-book-modal" data-id="<?= $book['ID']; ?>"><i class="bx bx-edit  btn-outline-dark"></i></a>
                                <a class="btn btn-sm  btn-outline-dark m-2 delete-book-btn" data-id="<?= $book['ID']; ?>"><i class="bx bx-trash btn-outline-dark"></i> </a>

                            </div>
                        </td>
                        <?php endif; ?>
                        <td>
                            <?php if (isset($book['Photo']) || !empty($book['Photo'])) : ?>
                                <img src="<?= asset('assets/uploads/' . $book['Photo']) ?>" alt="" class="d-block rounded m-3" width="80" id="uploadedAvatar">
                            <?php endif; ?>
                        </td>
                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?= $book['Title'] ?? '' ?></strong></td>
                        <td><?= $book['Author'] ?? '' ?></td>
                        <td><?= $book['CategoryID'] ?? '' ?></td>
                        <td><?= $book['ISBN'] ?? '' ?></td>
                        <td><?= $book['Quantity'] ?? '' ?></td>
                        <td><?= $book['CreatedAt'] ?? '' ?></td>


                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <!--/ Basic Bootstrap Table -->

    <hr class="my-5" />
</div>

<!-- create book modal -->

<div class="modal fade" id="new-book-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="row">
                <form id="create-form" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Add New Book</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">

                        <input type="hidden" name="action" value="create_book">

                        <div class="col mb-3">
                            <label for="image" class="form-label">Book Image:</label>
                            <input type="file" required id="image" name="image" class="form-control" placeholder="Choose File" />
                        </div>


                        <div class="col mb-3">
                            <label for="title" class="form-label">Book Title:</label>
                            <input required type="text" name="title" id="title" class="form-control" placeholder="Enter Title" />
                        </div>

                        <div class="col mb-3">
                            <label for="author" class="form-label">Book Author:</label>
                            <input required type="text" name="author" id="author" class="form-control" placeholder="Enter Author Name" />
                        </div>


                        <div class="col mb-3">
                            <label for="category" class="form-label">Book Category:</label>
                            <input required type="text" name="CategoryID" id="category" class="form-control" placeholder="Enter Category" />
                        </div>

                        <div class="col mb-3">
                            <label for="isbn" class="form-label">Book ISBN:</label>
                            <input required type="text" name="isbn" id="isbn" class="form-control" placeholder="Enter ISBN" />
                        </div>

                        <div class="col mb-3">
                            <label for="quantity" class="form-label">Book Quantity:</label>
                            <input required type="number" name="quantity" id="quantity" class="form-control" placeholder="Enter Quantity" />
                        </div>

                        <div class="col mb-3">
                            <label for="html5-datetime-local-input" class="col-md-2 col-form-label">Datetime</label><br>
                            <div class="col-md-12">
                                <input class="form-control" type="datetime-local" value="2021-06-18T12:30:00" id="html5-datetime-local-input" name="added_at" />
                            </div>
                        </div>


                        <div class="mb-3 mt-3">
                            <div id="alert-container"></div>
                        </div>
                        <div class="mb-3 mt-3">
                            <div id="additional-fields">

                            </div>
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

<!-- Udpate books model -->
<div class="modal fade" id="edit-book-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="update-form" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Update Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_book">
                    <input type="hidden" id="book_id" name="id" value="">
                    <div class="row ">
                        <div class="col mb-3">
                            <label for="title" class="form-label">Book Title:</label>
                            <input required type="text" name="title" id="title" class="form-control" placeholder="Enter Title" />
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col mb-3">
                            <label for="author" class="form-label">Book Author:</label>
                            <input required type="text" name="author" id="author" class="form-control" placeholder="Enter Author Name" />
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col mb-3">
                            <label for="category" class="form-label">Book Category:</label>
                            <input required type="text" name="categoryid" id="category" class="form-control" placeholder="Enter Category" />
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col mb-3">
                            <label for="isbn" class="form-label">Book ISBN:</label>
                            <input required type="text" name="isbn" id="isbn" class="form-control" placeholder="Enter ISBN" />
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col mb-3">
                            <label for="quantity" class="form-label">Book Quantity:</label>
                            <input required type="number" name="quantity" id="quantity" class="form-control" placeholder="Enter Quantity" />
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col mb-3">
                            <label for="html5-datetime-local-input" class="col-md-2 col-form-label">Datetime</label><br>
                            <div class="col-md-12">
                                <input class="form-control" type="datetime-local" value="2021-06-18T12:30:00" id="html5-datetime-local-input" name="added_at" />
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
                    <button type="button" class="btn btn-primary" id="update-book">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php
require_once('../layouts/footer.php');
?>
<script src="<?= asset('assets/forms-js/Books.js') ?>"></script>
<script>
    $(document).ready(function() {
        $("#searchInput").on("input", function() {
            var searchTerm = $(this).val().toLowerCase();

            // Loop through each row in the table body
            $("tbody tr").filter(function() {
                // Toggle the visibility based on the search term
                $(this).toggle($(this).text().toLowerCase().indexOf(searchTerm) > -1);
            });
        });

        // Initial setup for the date picker
        $('#datePicker').val(getFormattedDate(new Date()));



        // Function to update table rows based on the selected date
        function filterAppointmentsByDate(selectedDate) {
            console.log("selectedDate Date:", selectedDate); // Log each appointment date for debugging


            // Loop through each row in the table body
            $('tbody tr').each(function() {
                var appointmentDate = $(this).find('.appointment_date').text().trim();
                $(this).toggle(appointmentDate === selectedDate);
            });
        }

        // Event handler for the "Filter" button
        $('#clear').on('click', function() {
            location.reload();
        });

        // Event handler for date picker change
        $('#datePicker').on('change', function() {

            var selectedDate = $(this).val();
            alert(selectedDate);
            filterAppointmentsByDate(selectedDate);
        });

    });
</script>