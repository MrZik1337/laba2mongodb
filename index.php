<?php
require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/db_logic.php"; 
use MongoDB\Driver\Exception\Exception as MongoDBException;
$results_html = "";
$last_query_params_json = "";

if (isset($_GET['query_type'])) {
    $queryType = $_GET['query_type'];

    try {
        switch ($queryType) {
            case 'wards_by_nurse':
                if (isset($_GET['nurse_name']) && !empty(trim($_GET['nurse_name']))) {
                    $nurseName = trim($_GET['nurse_name']);
                    $results = findRoomsByNurse($nurseName);

                    $results_html .= "<h3>Результати запиту: Палати медсестри '" . htmlspecialchars($nurseName) . "'</h3>";
                    $found = false;
                    if (!empty($results)) {
                        $results_html .= "<ul>";
                        foreach ($results as $document) {
                            $rooms = $document['rooms']->getArrayCopy();
                            $results_html .= "<li>Палати: " . htmlspecialchars(implode(", ", $rooms)) . "</li>";
                        }
                        $results_html .= "</ul>";
                        $found = true;
                    }
                    if (!$found) {
                        $results_html .= "<p>Дані про чергування для цієї медсестри не знайдені.</p>";
                    }
                    $last_query_params_json = json_encode(['query_type' => 'wards_by_nurse', 'nurse_name' => $nurseName]);
                } else {
                     $results_html = "<p class='error'>Будь ласка, введіть ім'я медсестри.</p>";
                }
                break;

            case 'nurses_by_department':
                if (isset($_GET['department_name']) && !empty(trim($_GET['department_name']))) {
                    $department = trim($_GET['department_name']);
                    $uniqueNurses = findNursesByDepartment($department);

                    $results_html .= "<h3>Результати запиту: Медсестри відділення '" . htmlspecialchars($department) . "'</h3>";
                    if (!empty($uniqueNurses)) {
                        $results_html .= "<ul>";
                        foreach ($uniqueNurses as $nurse) {
                            $results_html .= "<li>" . htmlspecialchars($nurse) . "</li>";
                        }
                        $results_html .= "</ul>";
                    } else {
                        $results_html .= "<p>Дані про чергування у цьому відділенні не знайдені.</p>";
                    }
                    $last_query_params_json = json_encode(['query_type' => 'nurses_by_department', 'department_name' => $department]);
                } else {
                    $results_html = "<p class='error'>Будь ласка, введіть назву відділення.</p>";
                }
                break;

            case 'duties_by_shift':
                 if (isset($_GET['shift_name']) && !empty($_GET['shift_name']) && isset($_GET['department3_name']) && !empty(trim($_GET['department3_name']))) { 
                     $shift = $_GET['shift_name'];
                     $department = trim($_GET['department3_name']);
                     $results = findShiftsByShiftAndDepartment($shift, $department);

                     $results_html .= "<h3>Результати запиту: Чергування ('" . htmlspecialchars($shift) . "' зміна, відділення '" . htmlspecialchars($department) . "')</h3>";
                     $found = false;
                     if (!empty($results)) {
                         foreach ($results as $document) {
                             $found = true;
                             $results_html .= "<div style='border-bottom: 1px dashed #ccc; margin-bottom: 10px; padding-bottom: 10px;'>";
                             $date = $document['date'] ? $document['date']->toDateTime()->format('Y-m-d H:i') : 'Не вказано';
                             $nurses = $document['nurses'] ? implode(", ", $document['nurses']->getArrayCopy()) : 'Не вказано';
                             $rooms = $document['rooms'] ? implode(", ", $document['rooms']->getArrayCopy()) : 'Не вказано';

                             $results_html .= "Дата: " . htmlspecialchars($date) . "<br>";
                             $results_html .= "Медсестри: " . htmlspecialchars($nurses) . "<br>";
                             $results_html .= "Палати: " . htmlspecialchars($rooms) . "<br>";
                             $results_html .= "</div>";
                         }
                     }
                     if (!$found) {
                         $results_html .= "<p>Чергування за заданими критеріями не знайдені.</p>";
                     }
                     $last_query_params_json = json_encode(['query_type' => 'duties_by_shift', 'shift_name' => $shift, 'department3_name' => $department]); // Використовуємо duties_by_shift, shift_name, department3_name
                 } else {
                     $results_html = "<p class='error'>Будь ласка, оберіть зміну та введіть назву відділення.</p>";
                 }
                 break;

            default:
                $results_html = "<p>Оберіть параметри та виконайте запит.</p>";
                break;
        }

    } catch (MongoDBException $e) {
        $results_html = "<p class='error'>Помилка бази даних: " . htmlspecialchars($e->getMessage()) . "</p>";
        error_log("MongoDB Error: " . $e->getMessage());
    } catch (\Exception $e) {
         $results_html = "<p class='error'>Виникла помилка: " . htmlspecialchars($e->getMessage()) . "</p>";
         error_log("General Error: " . $e->getMessage());
    }
} else {
    $results_html = "<p>Оберіть параметри та виконайте запит.</p>";
}

include __DIR__ . "/template.php";
?>
