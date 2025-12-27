<!DOCTYPE html>
<html>
    <head>
        <title>Notification of Room Change</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            h2 { color: #2c3e50; }
            h4 { margin: 5px 0; }
            p { margin-bottom: 15px; }
            .section { margin-bottom: 20px; }
        </style>
    </head>

    <body>
        <p>Dear {{ $student->name ?? 'Student' }},</p>

        <p>This is to inform you that your hostel room has been changed as per administrative arrangements. Please find the details below:</p>

        <div class="section">
            <h4>Previous Accommodation Details:</h4>
            <ul>
                <li>Hotel Name: {{ $previousHotelName ?? 'N/A' }}</li>
                <li>Building Name: {{ $previousBuilding ?? 'N/A' }}</li>
                <li>Floor: {{ $previousFloor ?? 'N/A' }}</li>
                <li>Room Number: {{ $previousRoom ?? 'N/A' }}</li>
            </ul>
        </div>

        <div class="section">
            <h4>New Accommodation Details:</h4>
            <ul>
                <li>Hotel Name: {{ $currentHotelName ?? 'N/A' }}</li>
                <li>Building Name: {{ $currentBuilding ?? 'N/A' }}</li>
                <li>Floor: {{ $currentFloor ?? 'N/A' }}</li>
                <li>Room Number: {{ $currentRoom ?? 'N/A' }}</li>
            </ul>
        </div>

        <p>Effective Date of Change: <strong>{{ isset($dateOfChange) ? \Carbon\Carbon::parse($dateOfChange)->format('d M Y') : 'N/A' }}</strong></p>

        <p>You are requested to complete the transfer of your belongings and report to the hostel office for verification and key collection. Kindly ensure that your previous room is vacated in proper condition before moving to the new room.</p>

        <p>This change has been made to facilitate hostel management requirements, and your cooperation in this regard is appreciated. For any clarification or assistance, please contact the hostel office.</p>

        <p>
            Sincerely,<br>
            Warden / Hostel Administrator<br>
        </p>
        
    </body>
</html>
