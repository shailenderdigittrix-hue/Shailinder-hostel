<!DOCTYPE html>
<html>
    <head>
        <title>Notice Regarding Late Hostel Attendance</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            h2 { color: #2c3e50; }
            p { margin-bottom: 15px; }
        </style>
    </head>
    <body>
        <p>Dear {{ $student->name }},</p>

        <p>
            This is to formally notify you that your late entry to the hostel was recorded on 
            <strong>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</strong>. 
            As per the hostel regulations, all residents are required to adhere strictly to 
            the prescribed entry timings to ensure the safety and proper management of the premises.
        </p>

        <p>
            Please be advised that any further instances of late attendance will be viewed seriously 
            and may attract disciplinary action in accordance with the hostel rules and regulations. 
            You are therefore instructed to ensure timely return to the hostel henceforth.
        </p>

        <p>Your cooperation in maintaining discipline and compliance with hostel policies is appreciated.</p>

        <p>Sincerely,<br>
            Warden / Hostel Administrator
        </p>

    </body>
</html>
