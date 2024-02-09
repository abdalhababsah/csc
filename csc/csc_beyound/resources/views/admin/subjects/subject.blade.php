@extends('dash_layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="d-flex justify-content-between">
            <div class="py-3 px-3">
                <h6 class="m-0 font-weight-bold text-primary">Subjects</h6>
            </div>
            <div class="py-3 px-3">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addSubjectModal">Add Subject</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Assigned Students</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Action</th>
                            <th>View</th>
                            <th>Subject Chat</th>
                        </tr>
                    </thead>
                    <tbody id="subjectsTableBody">
                        <!-- Subjects will be loaded here by AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Subject Modal -->
<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1" role="dialog" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="addSubjectForm">
                @csrf <!-- Add CSRF token -->
                <div class="modal-header">
                    <h5 class="modal-title" id="addSubjectModalLabel">Add Subject</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="subjectName">Name:</label>
                        <input type="text" class="form-control" id="subjectName" name="sub_name" required>
                    </div>
                    <div class="form-group">
                        <label for="subjectDescription">Description:</label>
                        <textarea class="form-control" id="subjectDescription" name="body"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="subjectStatus">Status:</label>
                        <select class="form-control" id="subjectStatus" name="status">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="addSubjectButton">Add Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Subject Modal -->
<div class="modal fade" id="editSubjectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editSubjectForm">
                @csrf <!-- Add CSRF token -->
                @method('PUT') <!-- Add method spoofing for PUT request -->
                <div class="modal-header">
                    <h5 class="modal-title">Edit Subject</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editSubjectId">
                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text" name="sub_name" class="form-control" id="editSubjectName" required>
                    </div>
                    <div class="form-group">
                        <label>Description:</label>
                        <textarea name="body" class="form-control" id="editSubjectDescription"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status:</label>
                        <select name="status" class="form-control" id="editSubjectStatus" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Students:</label>
                        <select name="student_ids[]" class="form-control" id="editSubjectStudents" multiple>
                            <!-- Students options will be loaded dynamically via AJAX -->
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

<script>

// Function to fetch subjects data via AJAX and update the table
var logedinId = "{{ Auth::user()->id }}";
function fetchSubjectsAndUpdateTable() {
    $.ajax({
        type: 'GET',
        url: '{{ route('admin.subjects.index') }}', 
        dataType: 'json',
        success: function(response) {
            console.log(response); // Log the received data
            
            // Clear the existing table body
            $('#subjectsTableBody').empty();

            // Iterate through each subject in the response
            response.subjects.forEach(function(subject) {
                // Generate HTML for students assigned to the subject
                var studentsHtml = subject.students.map(function(student) {
                    return student.name + '<br>';
                }).join('');

                // Append a new row to the table with subject details
                $('#subjectsTableBody').append(`
                    <tr id="subjectRow_${subject.id}">
                        <td>${subject.sub_name}</td>
                        <td>${subject.body || ''}</td>
                        <td>${studentsHtml || 'No students assigned'}</td>
                        <td>${subject.status}</td>
                        <td>${subject.created_at}</td>
                        <td>
                            <button class="btn my-2 btn-primary editSubjectButton" data-id="${subject.id}" data-toggle="modal" data-target="#editSubjectModal">Edit</button>
                            <button class="btn btn-danger deleteSubjectButton" data-id="${subject.id}">Delete</button>
                        </td>
                        <td>
                            <a href="/admin/dashboard/studenttoclass/show/${subject.id}" class="btn btn-info">View</a>
                        </td>
                        <td>
                            <a href="/groupchat/${subject.id}" class=" btn btn-primary">Public</a>
                            <a href="/chat/${subject.id}" class="btn my-2 btn-warning">Private</a>
                        </td>
                        
                    </tr>
                `);
            });
        },
        error: function(error) {
            console.log(error);
            alert('Error fetching subjects');
        }
    });
}
$(document).ready(function() {
    fetchSubjectsAndUpdateTable(); 

    $('#addSubjectForm').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
        type: 'POST',
        url: '/admin/dashboard/subjects/store',
        data: formData,
        success: function(subject) {
            $('#addSubjectModal').modal('hide');
            fetchSubjectsAndUpdateTable(); 
        },
        error: function(error) {
            alert('Error adding subject');
        }
    });
});


    $(document).on('click', '.deleteSubjectButton', function() {
    var subjectId = $(this).data('id');
    if (confirm("Are you sure you want to delete this subject?")) {
        $.ajax({
            type: 'POST', 
            url: '/admin/dashboard/subjects/delete/' + subjectId, 
            data: {
                _method: 'DELETE', 
                _token: '{{ csrf_token() }}' 
            },
            success: function(response) {
                alert('Subject deleted successfully');
                fetchSubjectsAndUpdateTable(); 
            },
            error: function(error) {
                console.log(error);
                alert('Error deleting subject');
            }
        });
    }
});


// Event handler for opening the edit subject modal and populating it with data
$(document).on('click', '.editSubjectButton', function() {
    var subjectId = $(this).data('id');
    $.ajax({  
        url: '/admin/dashboard/subjects/' + subjectId + '/edit',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            $('#editSubjectId').val(data.subject.id);
            $('#editSubjectName').val(data.subject.sub_name);
            $('#editSubjectDescription').val(data.subject.body);
            $('#editSubjectStatus').val(data.subject.status);
            
            $('#editSubjectStudents').empty();
            
            data.allStudents.forEach(function(student) {
                $('#editSubjectStudents').append(`<option value="${student.id}">${student.name}</option>`);
            });
            
            $('#editSubjectModal').modal('show');
        }
    });
});
    $('#editSubjectForm').on('submit', function(e) {
    e.preventDefault();
    var subjectId = $('#editSubjectId').val();
    var formData = $(this).serialize();
    $.ajax({
        type: 'PUT',
        url: '/admin/dashboard/subjects/update/' + subjectId, // Update URL to include the subject ID parameter
        data: formData,
        success: function(updatedSubject) {
            $('#editSubjectModal').modal('hide');
            fetchSubjectsAndUpdateTable(); // Refresh table after updating subject
        },
        error: function(error) {
            console.log(error);
            alert('Error updating subject');
        }
    });
});

});


</script>
@endsection