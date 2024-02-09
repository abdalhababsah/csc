@extends('dash_layouts.master')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">Dashboard</h1>
        <a href="#" class="shadow-sm d-none d-sm-inline-block btn btn-sm btn-primary"><i
                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
    </div>

    <!-- Display the Subjects and Marks for the Logged-in Student -->
    @if (count($attendedSubjects) > 0)
        <div class="row">
            <div class="col-lg-12">
                <div class="mb-4 shadow card">
                    <div class="py-3 card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Subjects Attending</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Subject Name</th>
                                        <th>Mid-Term Mark</th>
                                        <th>Final Mark</th>
                                        <th>Actions</th> <!-- Added column for actions -->
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($attendedSubjects as $subject)
                                        <tr>
                                            <td>{{ $subject['subject_name'] }}</td>
                                            <td>{{ $subject['mid_mark'] }}</td>
                                            <td>{{ $subject['final_mark'] }}</td>
                                            <td>
                                                <!-- Chat action buttons -->
                                                <a href="/groupchat/{{ $subject['id'] }}" class="btn btn-primary btn-sm">Public Chat</a>
                                                <a href="/chat/{{ $subject['id'] }}" class="btn btn-warning btn-sm">Private Chat</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Content Row -->
</div>
@endsection
