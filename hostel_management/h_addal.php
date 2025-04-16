<?php
session_start();
include('db.php');
include("functions.php");

$success = $error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $student_id = $_POST['student_id'];
    $bed_id = $_POST['bed_id'];
    $alloc_from = $_POST['allocations_from'];
    $alloc_to = $_POST['allocations_to'];
    
    $campus_id = $_POST['campus_id']; // Used for display only
    $housekeeper_id = $_POST['housekeeper_id']; // Used for display only
    $user_id = $_SESSION['user_id'];

    // Insert allocation with housekeeper info
  // Insert ONLY fields that exist in the allocations table
  $stmt = $conn->prepare("INSERT INTO allocations (student_id, bed_id, allocations_from, allocations_to, user_id) VALUES (?, ?, ?, ?, ?)");

  $stmt->bind_param("iissi", $student_id, $bed_id, $alloc_from, $alloc_to, $user_id);


    if ($stmt->execute()) {
        $success = "Allocation added successfully!";
        log_action($conn, $_SESSION['user_id'], "Allocated a student");
    } else {
        $error = "Error adding allocation: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch students who are not reallocated or whose session has expired but haven't been reallocated yet
$students = $conn->query("
    SELECT s.student_id, s.full_name, s.gender
    FROM students s
    WHERE NOT EXISTS (
        SELECT 1 FROM allocations a
        WHERE a.student_id = s.student_id
        AND a.allocations_to > NOW()
    )
");





// Fetch beds + hostel + campus data
$beds = $conn->query("
    SELECT 
        beds.bed_id, beds.bed_number, 
        hostels.hostel_id, hostels.hostel_name, hostels.gender AS hostel_gender, 
        campus.campus_id, campus.campus_name,
        hostels.user_id AS housekeeper_id,
        users.username AS housekeeper_name
    FROM beds
    JOIN rooms ON beds.room_id = rooms.room_id
    JOIN hostels ON rooms.hostel_id = hostels.hostel_id
    JOIN campus ON hostels.campus_id = campus.campus_id
    JOIN users ON hostels.user_id = users.user_id
    WHERE beds.bed_id NOT IN (
        SELECT bed_id FROM allocations 
        WHERE allocations_to > NOW()
    )
");


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Allocation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <!-- jQuery (required for Select2 and Date Input handling) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Select2 JS -->
    

    <style>
        body {
            background-color: white;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #0f032b !important;
            color: white !important;
            border: none !important;
        }
        .btn-primary:hover {
            background-color: white !important;
            color: #0f032b !important;
            border: 1px solid #0f032b !important;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #0f032b;
        }

        .select2-results__option--highlighted {
    background-color: #0f032b !important;
    color: white !important;
}


.select2-container--default .select2-selection--single {
        background-color: white !important;
        color: #0f032b !important;
        border: 1px solid #ced4da;
        border-radius: 4px;
        height: 38px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #0f032b !important;
        line-height: 36px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }

    .select2-dropdown {
        background-color: white !important;
        color: #0f032b !important;
    }

    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h4 class="text-center fw-bold">Add Allocation</h4>
        <?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="post">
        <div class="mb-3">
    <label for="student_id">Select Student:</label>
    <select name="student_id" id="student_id" class="form-control custom-select2" required onchange="filterBeds()">
        <option value="">-- Select Student --</option>
        <?php while ($s = $students->fetch_assoc()): ?>
            <option value="<?= $s['student_id']; ?>" data-gender="<?= $s['gender']; ?>">
                <?= htmlspecialchars($s['full_name']); ?> (<?= $s['gender']; ?>)
            </option>
        <?php endwhile; ?>
    </select>
</div>


            <div class="mb-3">
                <label for="bed_id">Select Bed:</label>
                <select name="bed_id" id="bed_id" class="form-control" required onchange="updateHostelCampus()">
    <option value="">-- Select Bed --</option>
    <?php while ($b = $beds->fetch_assoc()): ?>
        <option 
            value="<?= $b['bed_id']; ?>" 
            data-gender="<?= $b['hostel_gender']; ?>"
            data-hostel-id="<?= $b['hostel_id']; ?>"
            data-hostel-name="<?= htmlspecialchars($b['hostel_name']); ?>"
            data-campus-id="<?= $b['campus_id']; ?>"
            data-campus-name="<?= htmlspecialchars($b['campus_name']); ?>"
            data-housekeeper-id="<?= $b['housekeeper_id']; ?>"
            data-housekeeper-name="<?= htmlspecialchars($b['housekeeper_name']); ?>"> <!-- Add housekeeper name -->
            <?= "Bed " . htmlspecialchars($b['bed_number']) . " - " . htmlspecialchars($b['hostel_name']) . " (" . $b['hostel_gender'] . ")" ?>
        </option>
    <?php endwhile; ?>
</select>

            </div>

            <div class="mb-3">
                <label for="housekeeper_id">Assigned Housekeeper:</label>
                <input type="text" id="housekeeper_display" class="form-control" readonly>
                <input type="hidden" name="housekeeper_id" id="housekeeper_id">
            </div>

            <div class="mb-3">
                <label for="campus_id">Campus:</label>
                <input type="text" id="campus_display" class="form-control" readonly>
                <input type="hidden" name="campus_id" id="campus_id">
            </div>

            <div class="mb-3">
                <label for="hostel_id">Hostel:</label>
                <input type="text" id="hostel_display" class="form-control" readonly>
                <input type="hidden" name="" id="hostel_id">
            </div>

            <div class="mb-3">
                <label for="allocations_from">From Date:</label>
                <input type="datetime-local" name="allocations_from" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="allocations_to">To Date:</label>
                <input type="datetime-local" name="allocations_to" class="form-control" required>
            </div>

            <div class="d-flex justify-content-between">
                <a href="allocation.php" class="btn btn-primary">Cancel</a>
                <button type="submit" class="btn btn-primary">Add Allocation</button>
            </div>
        </form>
    </div>
</div>

<script>
    function filterBeds() {
        const studentSelect = document.getElementById('student_id');
        const selectedGender = studentSelect.options[studentSelect.selectedIndex].getAttribute('data-gender');
        const bedSelect = document.getElementById('bed_id');

        for (let i = 0; i < bedSelect.options.length; i++) {
            const option = bedSelect.options[i];
            const bedGender = option.getAttribute('data-gender');

            option.style.display = (!bedGender || bedGender.toLowerCase() === selectedGender.toLowerCase()) ? 'block' : 'none';
        }

        bedSelect.selectedIndex = 0;
        updateHostelCampus(); // Reset hostel/campus display
    }

    function updateHostelCampus() {
    const bedSelect = document.getElementById('bed_id');
    const selectedOption = bedSelect.options[bedSelect.selectedIndex];

    const hostelName = selectedOption.getAttribute('data-hostel-name');
    const hostelId = selectedOption.getAttribute('data-hostel-id');
    const campusName = selectedOption.getAttribute('data-campus-name');
    const campusId = selectedOption.getAttribute('data-campus-id');
    const housekeeperId = selectedOption.getAttribute('data-housekeeper-id');
    const housekeeperName = selectedOption.getAttribute('data-housekeeper-name');  // Fetch housekeeper's name

    document.getElementById('hostel_display').value = hostelName || "";
    document.getElementById('hostel_id').value = hostelId || "";
    document.getElementById('campus_display').value = campusName || "";
    document.getElementById('campus_id').value = campusId || "";

    document.getElementById('housekeeper_display').value = housekeeperName || "No housekeeper assigned";  // Display housekeeper's name
    document.getElementById('housekeeper_id').value = housekeeperId || ""; // Hidden field to store housekeeper ID







    
}

</script>


<script>
$(document).ready(function() {
    $('#student_id').select2({
        placeholder: "-- Select Student --",
        width: '100%'
    });
});
</script>



<script>
    $(document).ready(function() {
    // Get current date and time
    var currentDate = new Date();
    currentDate.setDate(currentDate.getDate()); // Ensure it's the current day

    // Format current date and time to "yyyy-mm-ddThh:mm" (suitable for datetime-local input)
    var currentDateString = currentDate.toISOString().slice(0, 16); 

    // Set min value for allocations_from and allocations_to fields
    $("input[name='allocations_from']").attr("min", currentDateString);
    $("input[name='allocations_to']").attr("min", currentDateString);

    // Ensure allocations_to is always after allocations_from
    $("input[name='allocations_from'], input[name='allocations_to']").on('change', function() {
        var allocFrom = $("input[name='allocations_from']").val();
        var allocTo = $("input[name='allocations_to']").val();

        if (allocFrom && allocTo && allocTo < allocFrom) {
            alert("The 'To Date' must be later than the 'From Date'.");
            $("input[name='allocations_to']").val(''); // Clear the invalid value
        }
    });

    // Initialize Select2 for student select dropdown
    $('#student_id').select2({
        placeholder: "-- Select Student --",
        width: '100%'
    });
});

</script>


<script>
    $(document).ready(function() {
        $('#student_id').select2();
    });
</script>


</body>
</html>
