<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Card - {{ $student->first_name }} {{ $student->last_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 20px;
            color: #666;
        }
        .student-info {
            margin-bottom: 30px;
        }
        .student-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .student-info td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .student-info td:first-child {
            font-weight: bold;
            width: 30%;
            background-color: #f5f5f5;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .results-table th,
        .results-table td {
            padding: 12px;
            border: 1px solid #000;
            text-align: center;
        }
        .results-table th {
            background-color: #333;
            color: white;
            font-weight: bold;
        }
        .results-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .summary {
            margin-top: 30px;
            padding: 20px;
            background-color: #f5f5f5;
            border: 2px solid #000;
        }
        .summary h3 {
            margin-top: 0;
        }
        .summary table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .summary td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .grade {
            font-weight: bold;
            font-size: 16px;
        }
        .grade-A { color: #28a745; }
        .grade-B { color: #17a2b8; }
        .grade-C { color: #ffc107; }
        .grade-D { color: #dc3545; }
        .grade-F { color: #6c757d; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SCHOOL MANAGEMENT SYSTEM</h1>
        <h2>ACADEMIC REPORT CARD</h2>
        <p>{{ ucfirst(str_replace('-', ' ', $examType)) }} - {{ $term }}</p>
    </div>

    <div class="student-info">
        <table>
            <tr>
                <td>Student Name:</td>
                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
            </tr>
            <tr>
                <td>Admission Number:</td>
                <td>{{ $student->admission_number }}</td>
            </tr>
            <tr>
                <td>Class:</td>
                <td>{{ $student->class }}</td>
            </tr>
            <tr>
                <td>Admission Number:</td>
                <td>{{ $student->admission_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Academic Year:</td>
                <td>{{ $student->financial_year ?? date('Y') }}</td>
            </tr>
        </table>
    </div>

    <table class="results-table">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Marks Obtained</th>
                <th>Total Marks</th>
                <th>Percentage</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subjectResults as $result)
                <tr>
                    <td style="text-align: left;"><strong>{{ $result['subject'] }}</strong></td>
                    <td>{{ $result['marks'] !== null ? number_format($result['marks'], 2) : '-' }}</td>
                    <td>{{ number_format($result['total_marks'], 0) }}</td>
                    <td>{{ $result['percentage'] !== null ? number_format($result['percentage'], 2) . '%' : '-' }}</td>
                    <td>
                        <span class="grade grade-{{ substr($result['grade'], 0, 1) }}">
                            {{ $result['grade'] }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h3>Overall Performance</h3>
        <table>
            <tr>
                <td>Total Marks Obtained:</td>
                <td>{{ number_format($totalMarks, 2) }} / {{ number_format($totalPossible, 2) }}</td>
            </tr>
            <tr>
                <td>Overall Percentage:</td>
                <td><strong>{{ number_format($overallPercentage, 2) }}%</strong></td>
            </tr>
            <tr>
                <td>Overall Grade:</td>
                <td><strong class="grade grade-{{ substr($overallGrade, 0, 1) }}">{{ $overallGrade }}</strong></td>
            </tr>
            @if($attendanceStats)
                <tr>
                    <td>Attendance:</td>
                    <td>
                        Present: {{ $attendanceStats->present ?? 0 }} / 
                        Total: {{ $attendanceStats->total_days ?? 0 }} days
                        @if($attendanceStats->total_days > 0)
                            ({{ number_format((($attendanceStats->present ?? 0) / $attendanceStats->total_days) * 100, 1) }}%)
                        @endif
                    </td>
                </tr>
            @endif
        </table>
    </div>

    <div class="footer">
        <p>Generated on: {{ date('F d, Y') }}</p>
        <p>This is a computer-generated document and does not require a signature.</p>
    </div>
</body>
</html>

