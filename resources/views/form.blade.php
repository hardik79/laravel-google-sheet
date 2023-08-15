<!DOCTYPE html>
<html>

<head>
    <title>Spreadsheets Update Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="url"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        button[type="submit"] {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .message {
            margin-top: 10px;
            padding: 10px;
            text-align: center;
            background-color: #d4edda;
            color: #155724;
            border-radius: 3px;
        }

        .date-label {
            margin-top: 15px;
        }

        .date-input {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Update Spreadsheets</h1>

        @if(session('success'))
        <div class="message">
            {{ session('success') }}
        </div>
        @endif

        <form method="post" action="{{ route('submit-form') }}">
            @csrf
            <label for="text_field">Enter Spreadsheets URL:</label>
            <input type="url" id="text_field" name="spreadsheet_url" required placeholder="E.g., https://example.com/spreadsheet">

            <label class="date-label"  for="start_date">Start Date:</label>
            <input class="date-input" required type="date" id="start_date" name="start_date">

            <label class="date-label" for="end_date">End Date:</label>
            <input class="date-input" required type="date" id="end_date" name="end_date">

            <button type="submit">Update</button>
        </form>
    </div>
</body>

</html>