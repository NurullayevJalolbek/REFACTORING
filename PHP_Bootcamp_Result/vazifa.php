<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PWOT - Personal Work Off Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding-top: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .worked-off-row {
            background-color: #d4edda !important;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>PWOT - Personal Work Off Tracker</h1>

    <?php
    date_default_timezone_set('Asia/Tashkent');

    class PersonalWorkOffTracker {
        private $conn;

        public function __construct() {
            $this->conn = new PDO('mysql:host=localhost;dbname=vaqt1', 'root', 'root');
        }

        public function addRecord($arrived_at, $leaved_at) {
            $arrived_at_dt = new DateTime($arrived_at);
            $leaved_at_dt = new DateTime($leaved_at); 

            $interval = $arrived_at_dt->diff($leaved_at_dt);
             
            $hours = $interval->h + ($interval->days * 24); 
            $minutes = $interval->i;

            $required_work_off = sprintf('%02d:%02d:00', $hours, $minutes);

            $sql = "INSERT INTO vaqt (arrived_at, leaved_at, required_work_off) VALUES (:arrived_at, :leaved_at, :required_work_off)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':arrived_at', $arrived_at);
            $stmt->bindParam(':leaved_at', $leaved_at);
            $stmt->bindParam(':required_work_off', $required_work_off);

            if ($stmt->execute()) {
                echo "Ma'lumotlar bazaga qo'shildi.<br>";
            } else {
                echo "Ma'lumot bazaga qo'shilmadi.<br>";
            }
        }

        public function fetchRecords() {
            $sql = "SELECT * FROM vaqt";
            $result = $this->conn->query($sql);
            $total_hours = 0;
            $total_minutes = 0;

            if ($result->rowCount() > 0) {
                echo '<form id="work-off-form" action="" method="post">';
                echo '<table class="table table-striped">';
                echo '<thead><tr><th>#</th><th>Arrived at</th><th>Leaved at</th><th>Required work off</th><th>Worked off</th></tr></thead>';
                echo '<tbody>';
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $worked_off_class = $row["worked_off"] ? 'worked-off-row' : '';
                    echo '<tr class="' . $worked_off_class . '">';
                    echo '<td>' . $row["id"] . '</td>';
                    echo '<td>' . $row["arrived_at"] . '</td>';
                    echo '<td>' . $row["leaved_at"] . '</td>';
                    echo '<td>' . $row["required_work_off"] . '</td>';
                    echo '<td><button type="submit" name="worked_off[]" value="' . $row["id"] . '" class="btn btn-success"' . ($row["worked_off"] ? ' disabled' : '') . '>Done</button></td>';
                    echo '</tr>';

                    if (!$row["worked_off"]) {
                        list($hours, $minutes, $seconds) = explode(':', $row["required_work_off"]);
                        $total_hours += (int)$hours;
                        $total_minutes += (int)$minutes;
                    }
                }
                $total_hours += floor($total_minutes / 60);
                $total_minutes = $total_minutes % 60;

                echo '<tr><td colspan="4" style="text-align: right;">Total work off hours</td><td>' . $total_hours . ' hours and ' . $total_minutes . ' min.</td></tr>';
                echo '</tbody>';
                echo '</table>';
                echo '</form>';
            }
        }

        public function updateWorkedOff($id) {
            $sql = "UPDATE vaqt SET worked_off = 1 WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }
    }

    $tracker = new PersonalWorkOffTracker();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST["arrived_at"]) && isset($_POST["leaved_at"])) {
            if (!empty($_POST["arrived_at"]) && !empty($_POST["leaved_at"])) {
                $tracker->addRecord($_POST["arrived_at"], $_POST["leaved_at"]);
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            } else {
                echo "<p style='color: red;'>Iltimos ma'lumotlarni kiriting.</p>";
            }
        } elseif (isset($_POST["worked_off"])) {
            foreach ($_POST["worked_off"] as $id) {
                $tracker->updateWorkedOff($id);
            }
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        }
    }
    ?>

    <div class="form-container mb-4">
        <form action="" method="post">
            <div class="mb-3">
                <label for="arrived_at" class="form-label">Arrived At</label>
                <input type="datetime-local" id="arrived_at" name="arrived_at" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="leaved_at" class="form-label">Leaved At</label>
                <input type="datetime-local" id="leaved_at" name="leaved_at" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Submit</button>
        </form>
    </div>

    <?php
    $tracker->fetchRecords();
    ?>
</div>


</body>
</html>
