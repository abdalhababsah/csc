@extends('dash_layouts.master')


@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="d-flex justify-content-between">

        <div class=" py-3 px-3">
            <h6 class="m-0 font-weight-bold text-primary">Students</h6>
        </div>
        <div class=" py-3 px-3">
            <button class="m-0  btn-primary font-weight-bold =" data-toggle="modal" data-target="#addStudentModal"> Add Student</h6>
        </div>
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
                        @foreach($students as $student)
                            <tr>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->email }}</td> <!-- Adjust these fields based on your database structure -->
                                <td>      @if ($student->activated == 1)
                                    Active
                                @else
                                    Not Active
                                @endif
                                </td>
                                <td>{{ $student->created_at }}</td>
                                <td>
                                    <button class="btn btn-primary editStudentButton" data-id="{{ $student->id }}" data-toggle="modal" data-target="#editStudentModal">Edit</button>
                                    <button class="btn btn-danger deleteStudentButton" data-id="{{ $student->id }}" data-delete-url="{{ route('admin.students.destroy', $student->id) }}">Delete</button>
                                </td>
                            </tr>
                            
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- add modal --}}
<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" role="dialog" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form id="addStudentForm" data-action-url="{{ route('admin.students.store') }}">
           
            <div class="modal-header">
            <h5 class="modal-title" id="addStudentModalLabel">Add Student</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="studentName">Name:</label>
              <input type="text" class="form-control" id="studentName" name="name" required>
            </div>
            <div class="form-group">
              <label for="studentEmail">Email:</label>
              <input type="email" class="form-control" id="studentEmail" name="email" required>
            </div>
            <div class="form-group">
              <label for="studentPassword">Password:</label>
              <input type="password" class="form-control" id="studentPassword" name="password" required>
            </div>
            <div class="form-group">
              <label for="studentActivated">Account Activation:</label>
              <select class="form-control" id="studentActivated" name="activated">
                <option value="1">Active</option>
                <option value="0">Not Active</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Add Student</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
{{-- add modal --}}
        <!-- /.container-fluid -->
{{-- edit madal  --}}
<div class="modal fade" id="editStudentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editStudentForm" method="POST" >
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title">Edit Student</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <input type="hidden" name="id" value="{{ $student->id }}">
                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text" name="name" class="form-control" required value="{{ $student->name }}">
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" class="form-control" required value="{{ $student->email }}">
                    </div>
                    <div class="form-group">
                        <label>Account Activation:</label>
                        <select name="activated" class="form-control" required>
                            <option value="1">Active</option>
                            <option value="0">Not Active</option>
                        </select>
                    </div>
                    <!-- Add more fields as needed -->
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- end modal --}}

{{-- viewSubject --}}

{{--  --}}
<script>
$(document).ready(function() {
    // Add Student
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Add Student
    $('#addStudentForm').submit(function(e) {
        e.preventDefault();
        var actionUrl = $(this).attr('data-action-url'); 

        $.ajax({
            type: 'POST',
            url: actionUrl,
            data: $(this).serialize(),
            success: function(response) {
                $('#addStudentModal').modal('hide');
                location.reload();
            },
            error: function(error) {
                console.log(error);
            }
        });
    });

    $('.editStudentButton').click(function() {
        var studentId = $(this).data('id');
        var editUrl = $(this).data('edit-url'); 
        $('#editStudentForm').attr('data-action-url', editUrl); 
    });

    $('#editStudentForm').submit(function(e) {
        e.preventDefault();
        var actionUrl = $(this).data('action-url');

        $.ajax({
            type: 'PUT', 
            url: actionUrl,
            data: $(this).serialize(),
            success: function(response) {
                $('#editStudentModal').modal('hide');
                location.reload();
            },
            error: function(error) {
                console.log(error);
            }
        });
    });

    // Delete Student
    $('.deleteStudentButton').click(function() {
        var deleteUrl = $(this).data('delete-url');

        if (confirm("Are you sure you want to delete this student?")) {
            $.ajax({
                type: 'DELETE',
                url: deleteUrl,
                success: function(response) {
                    alert('Student deleted successfully');
                    location.reload();
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
    });
});

    </script>
    
@endsection