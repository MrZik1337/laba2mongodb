<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Пошук у базі даних лікарні</title>
    </head>
<body>

    <h1>Пошук у базі даних лікарні</h1>

    <h2>Знайти палати для медсестри</h2>
    <form action="" method="get">
        <input type="hidden" name="query_type" value="wards_by_nurse">
        <label for="nurse_name">Ім'я медсестри:</label>
        <input type="text" id="nurse_name" name="nurse_name" value="<?php echo htmlspecialchars($_GET['nurse_name'] ?? ''); ?>"><br><br>
        <input type="submit" value="Знайти палати">
    </form>

    <hr>

    <h2>Знайти медсестер за відділенням</h2>
    <form action="" method="get">
         <input type="hidden" name="query_type" value="nurses_by_department">
        <label for="department_name">Назва відділення:</label>
        <input type="text" id="department_name" name="department_name" value="<?php echo htmlspecialchars($_GET['department_name'] ?? ''); ?>"><br><br>
        <input type="submit" value="Знайти медсестер">
    </form>

     <hr>

    <h2>Знайти чергування за зміною та відділенням</h2>
    <form action="" method="get">
         <input type="hidden" name="query_type" value="duties_by_shift">
        <label for="shift_name">Зміна (Перша, Друга, Третя):</label>
        <select id="shift_name" name="shift_name">
            <option value="">-- Оберіть зміну --</option>
            <option value="Перша" <?php if (($_GET['shift_name'] ?? '') === 'Перша') echo 'selected'; ?>>Перша</option>
            <option value="Друга" <?php if (($_GET['shift_name'] ?? '') === 'Друга') echo 'selected'; ?>>Друга</option>
            <option value="Третя" <?php if (($_GET['shift_name'] ?? '') === 'Третя') echo 'selected'; ?>>Третя</option>
        </select><br><br>

        <label for="department3_name">Назва відділення:</label> <input type="text" id="department3_name" name="department3_name" value="<?php echo htmlspecialchars($_GET['department3_name'] ?? ''); ?>"><br><br>

        <input type="submit" value="Знайти чергування">
    </form>

    <hr>

    <div class="results">
        <h2>Результати запиту</h2>
        <?php
            echo $results_html ?? "<p>Оберіть параметри та виконайте запит.</p>";
        ?>
    </div>

    <hr>

    <div class="previous-results">
        <h2>Історія останнього запиту (з localStorage)</h2>
        <div id="lastQueryInfo">Завантаження...</div>
    </div>

    <script src="script.js"></script>
    <script>
        <?php if (!empty($last_query_params_json)): ?>
            const lastQueryParams = <?php echo $last_query_params_json; ?>;
            console.log("Параметри з PHP:", lastQueryParams);
            saveLastQuery(lastQueryParams);
        <?php else: ?>
             console.log("Параметри з PHP: порожні або запит не виконувався.");
        <?php endif; ?>

        document.getElementById('nurse_name').value = "<?php echo htmlspecialchars($_GET['nurse_name'] ?? ''); ?>";
        document.getElementById('department_name').value = "<?php echo htmlspecialchars($_GET['department_name'] ?? ''); ?>";
        document.getElementById('department3_name').value = "<?php echo htmlspecialchars($_GET['department3_name'] ?? ''); ?>";
        const shiftSelect = document.getElementById('shift_name');
        if (shiftSelect) {
            const selectedShift = "<?php echo htmlspecialchars($_GET['shift_name'] ?? ''); ?>";
            if (selectedShift) {
                shiftSelect.value = selectedShift;
            }
        }

    </script>

</body>
</html>
