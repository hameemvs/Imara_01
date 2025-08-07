<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/Borrowed_books.php';

$id = $_GET['id'] ?? null;
$Borrowed_BooksModel = new Borrowed_Books();
$Borrowed_Books = $Borrowed_BooksModel->getByBorrowId($id);
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-8">
            <h4 class=""><span class="text-muted fw-light">Dashboard /</span>Edit Borrowed Book </h4>
        </div>
        <div class="col-4 text-end">
            <!-- Back button to return to borrowed.php -->
            <a href="<?= url('views/admin/borrowed_books.php') ?>" class="btn btn-dark">Back to Borrowed Books</a>
        </div>
    </div>

    <div class="card m-3 p-5">

        <!-- /.card-header -->
        <div class="container">
            <form id="Borrowed_Books-form" action="<?= url('services/ajax_functions.php') ?>">
                <div class="row">
                    <div class="col-12">
                        <div id="alert-container"></div>
                    </div>
                    <input type="hidden" name="id" value="<?= $Borrowed_Books['ID']; ?>">
                    <input type="hidden" name="action" value="Borrowed_Books_update">

                    <div class="mb-3 col-6">
                        <label for="user_id" class="form-label">Member Id,Name:</label>
                        <input type="text" class="form-control" 
                        value="<?= $Borrowed_Books['UserID'] . ' - ' . $Borrowed_Books['user_name']; ?>"  name="user_id" readonly>
                    </div>

                    <div class="mb-3 col-6">
                        <label for="book_id" class="form-label">Book Id,Name:</label>
                        <input type="text" class="form-control" value="<?= $Borrowed_Books['BookID'].'-'.$Borrowed_Books['book_name']; ?>" name="book_id" readonly>
                    </div>
                    <div class=" mb-3 col-6">
                        <label for="book_status" class="form-label">Book Status:</label>
                        <input type="text" class="form-control" value="<?= $Borrowed_Books['BorrowStatus']; ?>" name="book_status" readonly>

                    </div>
                    <div class="mb-3 col-6">
                        <label for="borrowed_at" class="form-label">Borrowed At:</label>
                        <input type="date" class="form-control" value="<?= $Borrowed_Books['BorrowDate'] ; ?>" name="borrowed_at" readonly>
                    </div>

                    <div class="mb-3 col-6">
                        <label for="due_date" class="form-label">Due Date:</label>
                          <input type="date" class="form-control" value="<?= $Borrowed_Books['DueDate']; ?>" name="due_date" readonly>
                    </div>
                    <div class="mb-3 col-6">
                        <label for="returned_at" class="form-label">Returned At:</label>
                        <input type="date" class="form-control" name='returned_at' value="<?= $Borrowed_Books['ReturnDate']; ?>">
                    </div>
                    <div class="mb-3 col-6">
                        <label for="fine_status" class="form-label">Fine Status:</label>
                        <select class="form-control" name="fine_reason">
                            <option value="None" <?= $Borrowed_Books['FineStatus'] == 'None' ? 'selected' : '' ?>>None</option>
                            <option value="Late Return" <?= $Borrowed_Books['FineStatus'] == 'Late Return' ? 'selected' : '' ?>>Late Return</option>
                            <option value="Damaged" <?= $Borrowed_Books['FineStatus'] == 'Damaged' ? 'selected' : '' ?>>Damaged</option>
                            <option value="Lost" <?= $Borrowed_Books['FineStatus'] == 'Lost' ? 'selected' : '' ?>>Lost</option>
                        </select>
                    </div>
                    <div class=" mb-3 col-6">
                                <label for="paid_status" class="form-label">Paid Status:</label>
                                <select class="form-select" id="Paid_status" aria-label="Default select example" name="Paid_status" value="<?= $Borrowed_Books['PaidStatus']; ?>"  required>
                                    <option value="" class=" text-info "></option>
                                    <option value="none" class=" text-info ">No Fine</option>
                                    <option value="Paid" class=" text-success ">Paid</option>
                                    <option value="Unpaid" class=" text-danger ">unpaid</option>
                                </select>
                            </div> 
                            <div class="mb-3 col-6">
                                <label for="Paid_date" class="form-label">paid Date:</label>
                                <input type="date" class="form-control" value="<?= $Borrowed_Books['PaidDate']; ?>" name="Paid_date" >
                            </div>
                            <div class="mt-4 col-6 text-end">
                                <button type="button" class="btn rounded-pill btn-success" id="update-Borrowed_Books">Update</button>
                            </div>
                </div>
            </form>
        </div>
        <!-- /.card-body -->
    </div>
</div>

</div>
<?php require_once('../layouts/footer.php'); ?>
<!-- <script src="<?= asset('assets/forms-js/borrowed.js') ?>"></script> -->
<script>
    $(document).ready(function() {
        // Handle modal button click
        $('#update-Borrowed_Books').on('click', function(e) {
            e.preventDefault();

            // Get the form element
            var form = $('#Borrowed_Books-form')[0];
            $('#Borrowed_Books-form')[0].reportValidity();

            // Check form validity
            if (form.checkValidity()) {

                // Serialize the form data
                var formData = $('#Borrowed_Books-form').serialize();
                var formAction = $('#Borrowed_Books-form').attr('action');

                // Perform AJAX request
                $.ajax({
                    url: formAction,
                    type: 'POST',
                    data: formData, // Form data
                    dataType: 'json',
                    success: function(response) {
                        showAlert(response.message, response.success ? 'success' : 'danger');
                    },
                    error: function(error) {
                        // Handle the error
                        console.error('Error submitting the form:', error);
                    },
                    complete: function(response) {
                        // This will be executed regardless of success or error
                        console.log('Request complete:', response);
                    }
                });
            } else {
                var message = ('Form is not valid. Please check your inputs.');
                showAlert(message, 'danger');
            }
        });
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

        // Function to format date as YYYY-MM-DD
        function getFormattedDate(date) {
            var year = date.getFullYear();
            var month = (date.getMonth() + 1).toString().padStart(2, '0');
            var day = date.getDate().toString().padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // Function to update table rows based on the selected date
        function filterAppointmentsByDate(selectedDate) {
            // Loop through each row in the table body
            $('tbody tr').each(function() {
                var appointmentDate = $(this).find('.appointment_date').text(); // Assuming date is in the 12th column
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
            filterAppointmentsByDate(selectedDate);
        });

    });
</script>