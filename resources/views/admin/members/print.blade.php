<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $member->profile_id }} — {{ $member->full_name }}</title>
    <style>
        body { font-family: sans-serif; max-width: 850px; margin: 40px auto; color: #222; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { width: 35%; }
        @media print { button { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    <button type="button" onclick="window.print()">Print Profile</button>
    <h1>{{ $member->full_name }}</h1>
    <p>{{ $member->profile_id }}</p>
    <table>
        @foreach(['gender' => 'Gender', 'date_of_birth' => 'Date of birth', 'mobile_number' => 'Mobile', 'email' => 'Email', 'registration_date' => 'Registration date', 'marital_status' => 'Marital status', 'height' => 'Height', 'religion' => 'Religion', 'cast' => 'Caste', 'education' => 'Education', 'occupation' => 'Occupation', 'annual_income' => 'Annual income', 'city' => 'City', 'state' => 'State', 'country' => 'Country', 'about_me' => 'About me'] as $field => $label)
        @if(filled($member->{$field}))
        <tr><th>{{ $label }}</th><td>{{ $field === 'height' ? \App\Support\HeightFormatter::format($member->{$field}) : $member->{$field} }}</td></tr>
        @endif
        @endforeach
    </table>
</body>
</html>
