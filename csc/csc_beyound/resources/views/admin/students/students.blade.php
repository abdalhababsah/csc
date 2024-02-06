@extends('dash_layouts.master')

@section('content')
<div class="container-fluid">
    <div class="mb-4 shadow card">
        <div class="card-header d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Students</h6>
            <button class="btn btn-primary" data-toggle="modal" data-target="#addStudentModal">Add Student</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Activated</th>
                            <th>Joined At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="studentsTableBody">
                        <!-- Data will be populated here by jQuery -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addStudentForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStudentModalLabel">Add New Student</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Activation:</label>
                        <select name="activated" class="form-control" required>
                            <option value="1">Active</option>
                            <option value="0">Not Active</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editStudentForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editStudentId">
                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" id="editEmail" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Activation:</label>
                        <select name="activated" id="editActivated" class="form-control" required>
                            <option value="1">Active</option>
                            <option value="0">Not Active</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function fetchStudents() {
        $.ajax({
            url: "{{ route('students.index') }}",
            type: "GET",
            success: function(data) {
                $('#studentsTableBody').empty();
                $.each(data, function(index, student) {
                    $('#studentsTableBody').append(`
                        <tr>
                            <td>${student.name}</td>
                            <td>${student.email}</td>
                            <td>${student.activated ? 'Active' : 'Not Active'}</td>
                            <td>${student.created_at}</td>
                            <td>
                                <button class="btn btn-primary editStudentButton" data-id="${student.id}" ">Edit</button>
                                <button class="btn btn-danger deleteStudentButton" data-id="${student.id}">Delete</button>
                            </td>
                        </tr>
                    `);
                });
            }
        });
    }

    $('#addStudentForm').submit(function(e) {
    e.preventDefault();
    var $form = $(this); // Capture the form as a jQuery object
    $.ajax({
        type: 'POST',
        url: "{{ route('students.store') }}", // Ensure this route is correctly defined in your Laravel routes
        data: $form.serialize(), // Serialize the form data for submission
        success: function(response) {
            $('#addStudentModal').modal('hide'); // Hide the modal after successful data submission
            $form.trigger('reset'); // Reset the form fields to their initial values
            fetchStudents(); // Optionally, refresh the list of students if you have such a function
        },
        error: function(xhr, status, error) {
            // Handle errors here, if necessary
            console.error("Error: " + status + " " + error);
        }
    });
});

    $(document).on('click', '.editStudentButton', function() {
    var studentId = $(this).data('id');

    $.ajax({
        type: 'GET',
        url: '/students/' + studentId,
        success: function(data) {
            $('#editStudentId').val(data.id);
            $('#editName').val(data.name);
            $('#editEmail').val(data.email);
            $('#editActivated').val(data.activated.toString()); // Ensure this is a string if it's a boolean
            $('#editStudentModal').modal('show');
        }
    });
});

    $('#editStudentForm').submit(function(e) {
        e.preventDefault();
        var id = $('#editStudentId').val();
        $.ajax({
            type: 'PUT',
            url: "/students/" + id,
            data: $(this).serialize(),
            success: function() {
                $('#editStudentModal').modal('hide');
                fetchStudents();
            }
        });
    });

    $(document).on('click', '.deleteStudentButton', function() {
        var id = $(this).data('id');
        if(confirm("Are you sure?")) {
            $.ajax({
                type: 'DELETE',
                url: "/students/" + id,
                success: function() {
                    fetchStudents();
                }
            });
        }
    });

    fetchStudents();

});
$(document).on('click', '.editStudentButton', function() {
    var studentId = $(this).data('id');
    // Fetch student data from the server
    $.ajax({
        type: 'GET',
        url: '/students/' + studentId,
        success: function(data) {
            $('#editStudentId').val(data.id);
            $('#editName').val(data.name);
            $('#editEmail').val(data.email);
            $('#editActivated').val(data.activated.toString()); // Ensure this is a string if it's a boolean
            $('#editStudentModal').modal('show');
        }
    });
});
</script>
@endsection
