<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Academic Transcript - {{ $student->first_name }} {{ $student->last_name }}</title>
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
        .term-section {
            margin-bottom: 40px;
            page-break-inside: avoid;
        }
        .term-header {
            background-color: #333;
            color: white;
            padding: 10px;
            font-weight: bold;
            font-size: 18px;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .results-table th,
        .results-table td {
            padding: 10px;
            border: 1px solid #000;
            text-align: center;
        }
        .results-table th {
            background-color: #555;
            color: white;
            font-weight: bold;
        }
        .results-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .grade {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SCHOOL MANAGEMENT SYSTEM</h1>
        <h2>ACADEMIC TRANSCRIPT</h2>
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
        </table>
    </div>

    @foreach($transcriptData as $termData)
        <div class="term-section">
            <div class="term-header">
                {{ ucfirst(str_replace('-', ' ', $termData['exam_type'])) }} - {{ $termData['term'] }}
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
                    @php
                        $termTotal = 0;
                        $termPossible = 0;
                    @endphp
                    @foreach($termData['subjects'] as $result)
                        @php
                            if ($result['marks'] !== null) {
                                $termTotal += $result['marks'];
                            }
                            $termPossible += $result['total_marks'];
                        @endphp
                        <tr>
                            <td style="text-align: left;"><strong>{{ $result['subject'] }}</strong></td>
                            <td>{{ $result['marks'] !== null ? number_format($result['marks'], 2) : '-' }}</td>
                            <td>{{ number_format($result['total_marks'], 0) }}</td>
                            <td>{{ $result['percentage'] !== null ? number_format($result['percentage'], 2) . '%' : '-' }}</td>
                            <td><span class="grade">{{ $result['grade'] }}</span></td>
                        </tr>
                    @endforeach
                    <tr style="background-color: #e7f1ff; font-weight: bold;">
                        <td style="text-align: left;">TERM TOTAL</td>
                        <td>{{ number_format($termTotal, 2) }}</td>
                        <td>{{ number_format($termPossible, 2) }}</td>
                        <td>{{ $termPossible > 0 ? number_format(($termTotal / $termPossible) * 100, 2) . '%' : '-' }}</td>
                        <td>
                            @php
                                $termPercentage = $termPossible > 0 ? ($termTotal / $termPossible) * 100 : 0;
                                $termGrade = 'N/A';
                                if ($termPercentage >= 90) $termGrade = 'A+';
                                elseif ($termPercentage >= 80) $termGrade = 'A';
                                elseif ($termPercentage >= 70) $termGrade = 'B+';
                                elseif ($termPercentage >= 60) $termGrade = 'B';
                                elseif ($termPercentage >= 50) $termGrade = 'C+';
                                elseif ($termPercentage >= 40) $termGrade = 'C';
                                elseif ($termPercentage >= 30) $termGrade = 'D';
                                elseif ($termPercentage > 0) $termGrade = 'F';
                            @endphp
                            {{ $termGrade }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach

    <div class="footer">
        <p>Generated on: {{ date('F d, Y') }}</p>
        <p>This is a computer-generated document and does not require a signature.</p>
    </div>
</body>
</html>

