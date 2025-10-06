<!-- resources/views/reports.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticktax Attendance System</title>

    <!-- External Resources -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <!-- Local Styles -->
    <link rel="stylesheet" href="{{ asset('css/reports.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_side.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dash_nav.css') }}">
</head>

<body style="background-color: #fffffe;">
    @include('layout.navbar')
    @include('layout.sidebar')

<main>
    <div class="container outer-box mt-5 pt-5 pb-4">
        <div class="container inner-glass shadow p-4" id="main_box">
            <div class="mb-4">
                <h2 class="fw-bold" style="color: #232946;">Reports</h2>
                <small style="color: #989797;">Manage /</small>
                <small style="color: #444444;">Reports</small>
            </div>

            {{-- Tabs Navigation --}}
            <ul class="nav nav-tabs mb-4" id="reportTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial" type="button" role="tab">
                        Financial Report
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="fines-tab" data-bs-toggle="tab" data-bs-target="#fines" type="button" role="tab">
                        Fines & Payments Report
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="roster-tab" data-bs-toggle="tab" data-bs-target="#roster" type="button" role="tab">
                        Student Roster
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="reportTabsContent">
                {{-- Financial Report Tab --}}
                <div class="tab-pane fade show active" id="financial" role="tabpanel">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white fw-semibold">
                            Generate Financial Report
                        </div>
                        <div class="card-body">
                            {{-- Add Item Inputs --}}
                            <div class="row g-3 mb-3">
                                <div class="col-md-2">
                                    <input type="text" id="orNumber" class="form-control" placeholder="OR Number">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" id="item" class="form-control" placeholder="Item">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" id="unit" class="form-control" placeholder="Unit">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" id="quantity" class="form-control" placeholder="Quantity" min="1" value="1">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" id="cost" class="form-control" placeholder="Cost" min="0">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-success w-100" onclick="addItem()">Add</button>
                                </div>
                            </div>

                            {{-- Items Table --}}
                            <table class="table table-bordered" id="financialTable">
                                <thead>
                                    <tr>
                                        <th>OR Number</th>
                                        <th>Item</th>
                                        <th>Unit</th>
                                        <th>Quantity</th>
                                        <th>Cost</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="financialBody">
                                    {{-- Rows added dynamically --}}
                                </tbody>
                            </table>

                            <div class="text-end mt-3">
                                <strong>Total Amount: ₱<span id="totalAmount">0.00</span></strong>
                            </div>

                            {{-- Export Button triggers modal --}}
                            <div class="mt-4 text-end">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exportModal">
                                    <i class="fa-solid fa-file-export"></i> Export Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Fines & Payments Report Tab --}}
                <div class="tab-pane fade" id="fines" role="tabpanel">
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark fw-semibold">
                            Generate Fines & Payments Report
                        </div>
                        <div class="card-body">
                            <form action="{{ route('report.fines') }}" method="GET" target="_blank">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Month</label>
                                        <select class="form-select" name="month">
                                            <option value="All">All</option>
                                            <option value="January">January</option>
                                            <option value="February">February</option>
                                            <option value="March">March</option>
                                            <option value="April">April</option>
                                            <option value="May">May</option>
                                            <option value="June">June</option>
                                            <option value="July">July</option>
                                            <option value="August">August</option>
                                            <option value="September">September</option>
                                            <option value="October">October</option>
                                            <option value="November">November</option>
                                            <option value="December">December</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Year</label>
                                        <input type="number" class="form-control" name="year" placeholder="{{ date('Y') }}" min="2000" max="{{ date('Y') }}">
                                    </div>
                                    @if(auth()->user()->role === 'Super Admin')
                                    <div class="col-md-4">
                                        <label class="form-label">Organization</label>
                                        <input type="text" class="form-control" name="organization" placeholder="Org name">
                                    </div>
                                    @endif
                                </div>
                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fa-solid fa-scale-balanced"></i> Generate Fines Report
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Student Roster Tab --}}
                <div class="tab-pane fade" id="roster" role="tabpanel">
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white fw-semibold">
                            Generate Student Roster
                        </div>
                        <div class="card-body">
                            <form action="{{ route('report.studentRoster') }}" method="GET">
                                <div class="row g-3">
                                    @if(auth()->user()->role === 'Super Admin')
                                    <div class="col-md-4">
                                        <label class="form-label">Organization</label>
                                        <select class="form-select" name="organization">
                                            <option value="">All</option>
                                            <option value="ITS">ITS</option>
                                            <option value="PRAXIS">PRAXIS</option>
                                        </select>
                                    </div>
                               
                                    @endif
                                    <div class="col-md-4">
                                        <label class="form-label">Role</label>
                                        <select class="form-select" name="role">
                                            <option value="Member">Members</option>
                                            <option value="Officer">Officers</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa-solid fa-users-viewfinder"></i> Generate Roster
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Export Modal --}}
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('report.financial') }}" method="GET" target="_blank" class="modal-content">
                <input type="hidden" name="table_data" id="tableDataInput">

                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Finalize Report Export</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="eventName" class="form-label">Event Name</label>
                        <input type="text" name="event" id="eventName" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="cashOnHand" class="form-label">Cash on Hand (₱)</label>
                        <input type="number" name="cash_on_hand" id="cashOnHand" class="form-control" min="0" required>
                    </div>

                    <input type="hidden" name="export" value="pdf">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-file-export"></i> Confirm & Export
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.querySelector('#exportModal form').addEventListener('submit', function(e) {
    const rows = document.querySelectorAll('#financialBody tr');
    const tableData = [];

    rows.forEach(row => {
        const cols = row.querySelectorAll('td');

        const cleanCurrency = (value) => {
            return parseFloat(value.replace(/[₱,]/g, '').trim()) || 0;
        };

        tableData.push({
            or_number: cols[0].innerText.trim(),
            item: cols[1].innerText.trim(),
            unit: cols[2].innerText.trim(),
            quantity: parseFloat(cols[3].innerText) || 0,
            cost: cleanCurrency(cols[4].innerText),
            total: cleanCurrency(cols[5].innerText)
        });
    });

    document.getElementById('tableDataInput').value = JSON.stringify(tableData);
});
</script>
 <script>
    let totalAmount = 0;

    function addItem() {
        const orNumber = document.getElementById('orNumber').value;
        const item = document.getElementById('item').value;
        const unit = document.getElementById('unit').value;
        const quantity = parseInt(document.getElementById('quantity').value);
        const cost = parseFloat(document.getElementById('cost').value);

        if (!orNumber || !item || !unit || isNaN(quantity) || isNaN(cost)) {
            alert("Please fill in all fields correctly.");
            return;
        }

        const total = quantity * cost;

        const table = document.getElementById('financialBody');
        const row = document.createElement('tr');

        row.innerHTML = `
            <td>${orNumber}</td>
            <td>${item}</td>
            <td>${unit}</td>
            <td>${quantity}</td>
            <td>₱${cost.toFixed(2)}</td>
            <td>₱${total.toFixed(2)}</td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this, ${total})">Remove</button></td>
        `;

        table.appendChild(row);
        totalAmount += total;
        document.getElementById('totalAmount').innerText = totalAmount.toFixed(2);

        // Reset inputs
        document.getElementById('orNumber').value = '';
        document.getElementById('item').value = '';
        document.getElementById('unit').value = '';
        document.getElementById('quantity').value = 1;
        document.getElementById('cost').value = '';
    }

    function removeRow(button, amount) {
        const row = button.closest('tr');
        row.remove();
        totalAmount -= amount;
        document.getElementById('totalAmount').innerText = totalAmount.toFixed(2);
    }
</script>
</body>

</html>