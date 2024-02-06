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
                        </tr>
                    </thead>
                    <tbody id="subjectsTableBody">
                        @foreach($subjects as $subject)
                            <tr>
                                <td>{{ $subject->sub_name }}</td>
                                <td>{{ $subject->body }}</td>
                                <td>
                                    @if($subject->students->isEmpty())
                                        No students assigned
                                    @else
                                        {{ $subject->students->count() }} student(s)
                                        @foreach($subject->students as $student)
                                            {{ $student->name }}<br>
                                        @endforeach
                                    @endif
                                </td>
                                <td>{{ $subject->status }}</td>
                                <td>{{ $subject->created_at }}</td>
                                <td>
                                    <button class="btn btn-primary editSubjectButton" data-id="{{ $subject->id }}" data-toggle="modal" data-target="#editSubjectModal">Edit</button>
                                    <button class="btn btn-danger deleteSubjectButton" data-id="{{ $subject->id }}">Delete</button>
                                </td>
                                <td>
                                    <!-- View Subject Button or Link -->
                                    <a href="{{ route('admin.studenttoclass.index', ['subjectId' => $subject->id]) }}" class="btn btn-info">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1" role="dialog" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="addSubjectForm">
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
                    <button type="submit" class="btn btn-primary">Add Subject</button>
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
                            @foreach($allStudents as $student)
                                <option value="{{ $student->id }}">{{ $student->name }}</option>
                            @endforeach
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
$(document).ready(function() {
    // Add subject
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('.editSubjectButton').click(function() {
    var subjectId = $(this).data('id');
    $.ajax({
        type: 'GET',
        url: '/admin/dashboard/subjects/' + subjectId + '/edit',
        success: function(data) {
            $('#editSubjectId').val(data.id);
            $('#editSubjectName').val(data.sub_name);
            $('#editSubjectDescription').val(data.body);
            $('#editSubjectStatus').val(data.status);

            $('#editSubjectStudents').val([]); 

            var assignedStudents = data.students; // Assuming this is how you get the student IDs
            $('#editSubjectStudents').val(assignedStudents).trigger('change');

            $('#editSubjectModal').modal('show');
        },
        error: function() {
            alert('Could not retrieve subject details');
        }
    });
});


    $('#addSubjectForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: '/admin/dashboard/subjects', // Adjust this URL to your project's requirements
            data: $(this).serialize(),
            success: function(response) {
                $('#addSubjectModal').modal('hide');
                location.reload(); // Reload the page to show the new subject
            },
            error: function(error) {
                console.log(error);
                // Handle error
            }
        });
    });
    $('#editSubjectForm').submit(function(e) {
    e.preventDefault(); 

    var subjectId = $('input[name=id]').val(); 

    $.ajax({
        type: 'PUT', 
        url: '/admin/dashboard/subjects/' + subjectId, 
        data: $(this).serialize(), 
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
        },
        success: function(response) {
            $('#editSubjectModal').modal('hide');
            location.reload(); 
        },
        error: function(error) {
            console.log(error);
            alert('Error updating student');
        }
    });
});


    // Update subject
    $('#editSubjectForm').submit(function(e) {
        e.preventDefault();
        var subjectId = $('input[name=id]').val();
        $.ajax({
            type: 'POST',
            url: '/admin/dashboard/subjects/update/' + subjectId,
            data: $(this).serialize(),
            success: function(response) {
                $('#editSubjectModal').modal('hide');
            },
            error: function(error) {
                console.log(error);
            }
        });
    });


  

    // Delete subject
    $('.deleteSubjectButton').click(function() {
        var subjectId = $(this).data('id');
        if (confirm("Are you sure you want to delete this subject?")) {
            $.ajax({
                type: 'DELETE',
                url: '/admin/dashboard/subjects/' + subjectId, 
                success: function(response) {
                    alert('Subject deleted successfully');
                    location.reload();
                },
                error: function(error) {
                    console.log(error);
                    alert('Error deleting subject');
                }
            });
        }
    });
});


</script>
@endsection