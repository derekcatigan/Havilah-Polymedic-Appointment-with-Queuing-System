<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Queue Status Update</title>
</head>

<body>
    <p>Hello {{ $queue->patient->name }},</p>

    <p>Your queue status has been updated:</p>

    <ul>
        <li>Doctor: Dr. {{ $queue->doctor->name }}</li>
        <li>Queue Number: {{ $queue->queue_number }}</li>
        <li>Status: {{ ucfirst($queue->queue_status) }}</li>
    </ul>

    <p>{{ $statusMessage }}</p>

    <p>Thank you,<br>Havilah Polymedic Hospital</p>
</body>

</html>