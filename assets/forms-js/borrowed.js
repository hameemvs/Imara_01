
$(document).ready(function () {
    $('#create').on('click', function () {
        var form = $('#create-form')[0] ?? null;
        if (!form) console.log('Something went wrong..');
        
        var url = $('#create-form').attr('action');
        if (form.checkValidity() && form.reportValidity()) {
            var formData = new FormData(form);
            // Perform AJAX request
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false, // Don't set content type
                processData: false, // Don't process the data
                dataType: 'json',
                success: function (response) {
                    showAlert(response.message, response.success ? 'primary' : 'danger');
                    if (response.success) {
                        $('#createborrowedBookModal').modal('hide');
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    }
                },
                error: function (error) {
                    // Handle the error
                    console.error('Error submitting the form:', error);
                    showAlert('Something went wrong..!', 'danger');
                },
                complete: function (response) {
                    // This will be executed regardless of success or error
                    console.log('Request complete:', response);

                }
            });


        } else {
            showAlert('Form is not valid. Please check your inputs.', 'danger');
        }
    });

    
    $('.edit-borrowed-book-btn').on('click', async function () {
        var borrowed_book_id = $(this).data('id');
        await getborrowedbookById(borrowed_book_id);
    })
   
    $('.delete-borrowed-book-btn').on('click', async function () {
        var borrowed_book_id = $(this).data('id');
        var is_confirm = confirm('Are you sure,Do you want to delete?');
        if (is_confirm) await deleteById(borrowed_book_id);
    })

    $('#update-book').on('click', function () {
        
        // Get the form element
        var form = $('#update-form')[0];
        form.reportValidity();

        // Check form validity
        if (form.checkValidity()) {
            // Serialize the form data
            var url = $('#update-form').attr('action');
            var formData = new FormData($('#update-form')[0]);

            // Perform AJAX request
            $.ajax({
                url: url,
                type: 'POST',
                data: formData, // Form data
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function (response) {
                    showAlert(response.message, response.success ? 'primary' : 'danger', 'edit-alert-container');
                    if (response.success) {
                        $('#edit-book-modal').modal('hide');
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    }
                },
                error: function (error) {
                    // Handle the error
                    console.error('Error submitting the form:', error);
                },
                complete: function (response) {
                    // This will be executed regardless of success or error
                    console.log('Request complete:', response);
                }
            });
        } else {
            var message = ('Form is not valid. Please check your inputs.');
            showAlert(message, 'danger');
        }
    });

    $.ajax({
        url: url,
        type: 'GET',
        data: {
            borrowed_book_id : id,
            action: 'get_borrowed_book'
        }, // Form data
        dataType: 'json',
        success: function (response) {
            console.log(response);

            showAlert(response.message, response.success ? 'primary' : 'danger');
            if (response.success) {
                var book_status = response.data.book_status;
                var returned_at = response.data.returned_at;
       
                $('#update_borrowed_books #book_status').val(book_status);
                $('#update_borrowed_books #returned_at').val(returned_at);

                $('#update_borrowed_books').modal('show');
            }
        },
        error: function (error) {
            // Handle the error
            console.error('Error submitting the form:', error);
        },
        complete: function (response) {
            // This will be executed regardless of success or error
            console.log('Request complete:', response);
        }
    });
}
);

async function getborrowedbookById(id) {
    var url = $('#Borrowed_Books-form').attr('action');
    $('#edit-additional-fields').empty();

    // Perform AJAX request
    $.ajax({
        url: url,
        type: 'GET',
        data: {
            borrowed_book_id : id,
            action: 'get_borrowed_book'
        }, // Form data
        dataType: 'json',
        success: function (response) {
            console.log(response);

            showAlert(response.message, response.success ? 'primary' : 'danger');
            if (response.success) {
                var book_status = response.data.book_status;
                var returned_at = response.data.returned_at;
       
                $('#update_borrowed_books #book_status').val(book_status);
                $('#update_borrowed_books #returned_at').val(returned_at);

                $('#update_borrowed_books').modal('show');
            }
        },
        error: function (error) {
            // Handle the error
            console.error('Error submitting the form:', error);
        },
        complete: function (response) {
            // This will be executed regardless of success or error
            console.log('Request complete:', response);
        }
    });
}
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

    // Function to format date as YYYY-MM-DD
    function getFormattedDate(date) {
        var year = date.getFullYear();
        var month = (date.getMonth() + 1).toString().padStart(2, '0');
        var day = date.getDate().toString().padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

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
 // For User ID Searchable Dropdown
 const userSearchInput = document.getElementById('user-search');
const userList = document.getElementById('user-list');
const userHiddenInput = document.getElementById('user_id');
const userItems = userList.getElementsByTagName('li');

userSearchInput.addEventListener('input', function() {
    const filter = userSearchInput.value.toLowerCase();

    Array.from(userItems).forEach(item => {
        if (item.textContent.toLowerCase().includes(filter)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });

    userList.style.display = filter ? 'block' : 'none';
});

Array.from(userItems).forEach(item => {
    item.addEventListener('click', function() {
        if (item.getAttribute('data-value')) {
            userHiddenInput.value = item.getAttribute('data-value');
            userSearchInput.value = item.textContent;
            userList.style.display = 'none';
        }
    });
});

document.addEventListener('click', function(event) {
    if (!userList.contains(event.target) && event.target !== userSearchInput) {
        userList.style.display = 'none';
    }
});

// For Book ID Searchable Dropdown
const bookSearchInput = document.getElementById('book-search');
const bookList = document.getElementById('book-list');
const bookHiddenInput = document.getElementById('book_id');
const bookItems = bookList.getElementsByTagName('li');

bookSearchInput.addEventListener('input', function() {
    const filter = bookSearchInput.value.toLowerCase();

    Array.from(bookItems).forEach(item => {
        if (item.textContent.toLowerCase().includes(filter)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });

    bookList.style.display = filter ? 'block' : 'none';
});

Array.from(bookItems).forEach(item => {
    item.addEventListener('click', function() {
        if (item.getAttribute('data-value')) {
            bookHiddenInput.value = item.getAttribute('data-value');
            bookSearchInput.value = item.textContent;
            bookList.style.display = 'none';
        }
    });
});


document.addEventListener('click', function(event) {
    if (!bookList.contains(event.target) && event.target !== bookSearchInput) {
        bookList.style.display = 'none';
    }
});

$(document).on('click', '.approve-request, .decline-request', function () {
    const requestId = $(this).data('id');
    const action = $(this).hasClass('approve-request') ? 'approve' : 'reject';

    $.ajax({
        url: '/Puttalalm-Book-Zone/services/ajax_functions.php', // Adjust path as needed
        type: 'POST',
        dataType: 'json',
        data: {
            action: action,
            request_id: requestId
        },
        success: function (response) {
            if (response.success) {
                alert(response.message);
                location.reload(); // Reload the page to reflect changes
            } else {
                alert(response.message);
            }
        },
        error: function () {
            alert('An error occurred while processing the request.');
        }
    });
});
;
// $(document).ready(function() {
//     $('#update-Borrowed_Books').on('click', function(e) {
//         e.preventDefault();

//         var form = $('#Borrowed_Books-form')[0];
//         $('#Borrowed_Books-form')[0].reportValidity();

//         if (form.checkValidity()) {
//             var formData = $('#Borrowed_Books-form').serialize();
//             var formAction = $('#Borrowed_Books-form').attr('action');

//             $.ajax({
//                 url: formAction,
//                 type: 'POST',
//                 data: formData,
//                 dataType: 'json',
//                 success: function(response) {
//                     showAlert(response.message, response.success ? 'success' : 'danger');
//                     if (response.success) {
//                         // If a fine was applied
//                         if (response.fineApplied) {
//                             alert('Fine applied: ' + response.fineAmount);
//                         }
//                     }
//                 },
//                 error: function(error) {
//                     console.error('Error submitting the form:', error);
//                 }
//             });
//         } else {
//             showAlert('Form is not valid. Please check your inputs.', 'danger');
//         }
//     });
// });


$(document).ready(function () {
    $('#bookRequestForm').on('submit', function (e) {
        e.preventDefault();
        var bookId = $('#book_id').val();

        if (!bookId) {
            alert('Please select a book.');
            return;
        }

        $.ajax({
            url: 'services/ajax_functions.php', // Adjust to the path of your PHP file
            type: 'POST',
            data: {
                action: 'request_book',
                book_id: bookId
            },
            success: function (response) {
                var data = JSON.parse(response);
                $('#requestResponse').html('<div class="alert alert-' + (data.success ? 'success' : 'danger') + '">' + data.message + '</div>');

                if (data.success) {
                    $('#bookRequestForm')[0].reset();
                }
            },
            error: function () {
                alert('An error occurred while processing your request.');
            }
        });
    });
});

$('#request-form').on('submit', function (e) {
    e.preventDefault();
    const formData = $(this).serialize();

    $.ajax({
        url: 'services/ajax_functions.php',
        type: 'POST',
        data: formData,
        success: function (response) {
            const res = JSON.parse(response);
            if (res.success) {
                alert(res.message);
                location.reload();
            } else {
                alert(res.message); // Show error message
            }
        },
        error: function () {
            alert('An unexpected error occurred. Please try again.');
        }
    });
});




