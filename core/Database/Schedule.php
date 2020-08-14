<?php
    SQLDatabase::createTable("schedule", [
        "homeTeamID"=>"varchar(191)",
        "awayTeamID"=>"varchar(191)",
        "gameWeek"=>"varchar(191)",
    ]);

    $scheduleQuery =
        "INSERT INTO `" . SQLDatabase::TABLE_PREFIX . "schedule` (homeTeamID, awayTeamID, gameWeek)
        VALUES
        (1, 2, 1),(3, 4, 1),
        (1, 3, 2),(2, 4, 2),
        (1, 4, 3),(2, 3, 3)";

    $connection->query($scheduleQuery);

    echo("Schedule table has been created!<br>");
