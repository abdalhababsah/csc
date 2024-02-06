@extends('dash_layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ $subject->sub_name }} Students</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Mid Mark</th>
                            <th>Final Mark</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subject->students as $student)
                            <tr>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->email }}</td>
                                <td id="midMark_{{ $student->id }}">{{ $student->pivot->mid_mark }}</td>
                                <td id="finalMark_{{ $student->id }}">{{ $student->pivot->mark }}</td>
                                <td>
                                    <!-- Button trigger modal for editing marks -->
                                    <button class="btn btn-primary editMarkButton" data-id="{{ $student->id }}" data-midmark="{{ $student->pivot->mid_mark }}" data-mark="{{ $student->pivot->mark }}" data-toggle="modal" data-target="#editMarksModal">Edit Marks</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Marks Modal -->
    <!-- Edit Marks Modal -->
<div class="modal fade" id="editMarksModal" tabindex="-1" role="dialog" aria-labelledby="editMarksModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editMarksForm" action="{{ route('admin.studenttoclass.updateMarks') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editMarksModalLabel">Edit Student Marks</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="studentId" name="student_id">
                    <input type="hidden" id="subjectId" name="subject_id" value="{{ $subject->id }}">
                    <div class="form-group">
                        <label for="midMark">Mid Mark:</label>
                        <input type="number" class="form-control" id="midMark" name="mid_mark" required>
                    </div>
                    <div class="form-group">
                        <label for="finalMark">Final Mark:</label>
                        <input type="number" class="form-control" id="finalMark" name="mark" required>
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

</div>
<script>

$(document).ready(function() {
    // When an edit mark button is clicked
    $('.editMarkButton').on('click', function() {
        var studentId = $(this).data('id'); // Get the student's ID
        var midMark = $(this).data('midmark'); // Get the student's mid mark
        var mark = $(this).data('mark'); // Get the student's final mark
        
        // Populate the modal with the current marks
        $('#editMarksModal #studentId').val(studentId);
        $('#editMarksModal #midMark').val(midMark);
        $('#editMarksModal #finalMark').val(mark);
    });

    // When the form inside the modal is submitted
    $('#editMarksForm').submit(function(e) {
        e.preventDefault(); // Prevent the default form submission

        var formData = $(this).serialize(); // Serialize the form data
        var studentId = $('#studentId').val(); // Get the student ID from the hidden input

        // Perform the AJAX request
        $.ajax({
            type: "POST",
            url: $(this).attr('action'), // Get the URL to submit the form data
            data: formData,
            success: function(response) {
                // Update the marks in the table directly without reloading the page
                $('#midMark_' + studentId).text(response.mid_mark);
                $('#finalMark_' + studentId).text(response.mark);

                // Hide the modal
                $('#editMarksModal').modal('hide');

                // Show success alert
                alert("Marks updated successfully");
            },
            error: function(response) {
                // Show error alert
                alert("Error updating marks");
            }
        });
    });
});

</script>
@endsection
