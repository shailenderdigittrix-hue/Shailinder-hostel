<!DOCTYPE html>
<html>
    <head>
        <title>Submission of Late Arrival and Unmarked Attendance Report – {{ \Carbon\Carbon::now()->format('d M Y') }}</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.5; color: #333; }
            h2 { color: #2c3e50; }
            h3 { color: #34495e; margin-top: 20px; }
            ul { padding-left: 20px; }
            li { margin-bottom: 5px; }
            .section { margin-bottom: 20px; }
        </style>
    </head>
    <body>
        <p> Dear Admin </p>

        <p>
            I am writing to submit the report of hostel attendance for 
            <strong>{{ \Carbon\Carbon::now()->format('d M Y') }}</strong>. 
            Please find below the details of students who arrived late and those who have not marked their attendance.
        </p>

        <div class="section">
            <h3>1. Students with Late Arrival:</h3>
            @if($lateStudents->isNotEmpty())
                <ul>
                    @foreach($lateStudents as $student)
                        <li>{{ $student->first_name }} {{ $student->last_name }} ({{ $student->enrollment_no }})</li>
                    @endforeach
                </ul>
            @else
                <p>No late students.</p>
            @endif
        </div>

        <div class="section">
            <h3>2. Students Who Have Not Marked Attendance:</h3>
            @if($absentStudents->isNotEmpty())
                <ul>
                    @foreach($absentStudents as $student)
                        <li>{{ $student->first_name }} {{ $student->last_name }} ({{ $student->enrollment_no }})</li>
                    @endforeach
                </ul>
            @else
                <p>All students have marked their attendance.</p>
            @endif
        </div>

        <p>Kindly review the details and advise if any further action is required. The attendance records have also been updated in the hostel log for your reference.</p>

        <p>Please let me know if you require any additional information.</p>

        <p>Sincerely,<br>
        <p>Hostel Attendance In-Charge / Resident Assistant</p>
    </body>
</html>
