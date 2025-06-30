<!DOCTYPE html>
<html>
<head>
  <title>CSV Preview</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
  <div class="container mt-4">
    <h4>CSV Preview</h4>
    <form method="POST" action="{{ route('import.confirm') }}">
      @csrf
      <table class="table table-bordered table-sm">
        <thead class="table-light">
          <tr>
            <th>No</th>
            <th>ID</th>
            <th>Name</th>
            <th>Gender</th>
            <th>Course</th>
            <th>Year</th>
            <th>Units</th>
            <th>Section</th>
            <th>Contact</th>
            <th>Birth Date</th>
            <th>Address</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach($previewData as $row)
          <tr class="{{ $row['status'] === 'Duplicate' ? 'table-warning' : ($row['status'] === 'Updated' ? 'table-info' : 'table-success') }}">
            @foreach($row as $value)
            <td>{{ $value }}</td>
            @endforeach
          </tr>
          @endforeach
        </tbody>
      </table>
      <div class="d-flex justify-content-end mt-3">
        <button type="submit" class="btn btn-primary">Confirm Import</button>
      </div>
    </form>
  </div>
</body>
</html>
