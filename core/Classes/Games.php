<?php
	/**
	* Class Games
	*
	* @author John Ellison
	*/
	class Games
	{
		public function getSchedule()
		{
			$database = SQLDatabase::connect();

			$result = $database->prepare("
				SELECT `homeTeamId`, `awayTeamId`, `gameWeek`
				FROM `" . SQLDatabase::TABLE_PREFIX . "schedule`
			");

			$result->execute();
			$result->bind_result($homeTeamId, $awayTeamId, $gameWeek);
			$schedule = [];

			while ($result->fetch())
			{
				$schedule[] = [
					"homeTeamId"=>$homeTeamId,
					"awayTeamId"=>$awayTeamId,
					"gameWeek"=>$gameWeek
				];
			}
			return $schedule;
		}

		public function simulateCurrentWeekGames()
		{
			$schedule = Games::getSchedule();
			$currentWeek = Utilities::getCurrentWeek();

			for($i = 0; $i < count($schedule); $i++)
			{
				if($schedule[$i]['gameWeek'] == $currentWeek)
				{
					Games::simulateGame($schedule[$i]['homeTeamId'], $schedule[$i]['awayTeamId']);
				}
			}
		}

		public function simulateGame($homeTeamId, $awayTeamId)
		{
			//TODO: We first want to evaluate both teams
			//TODO: Update the simulation to actually take stats into account
			if ($homeTeamId > $awayTeamId)
			{
				Games::updateWinsAndLoses($homeTeamId, $awayTeamId);
				echo "Team " . $homeTeamId . " has beaten Team " . $awayTeamId . "<br>";
			}
			else
			{
				Games::updateWinsAndLoses($awayTeamId, $homeTeamId);
				echo "Team " . $awayTeamId . " has beaten Team " . $homeTeamId . "<br>";
			}
		}

		public function updateWinsAndLoses($winningTeamId, $losingTeamId)
		{
			$database = SQLDatabase::connect();

			//TODO: Try to figure out if this is doable with less queries
			$winningTeamQuery = $database->query("
				UPDATE `" . SQLDatabase::TABLE_PREFIX . "schools` SET `totalWins` = `totalWins` + 1, `seasonWins` = `seasonWins` + 1 WHERE schoolId = '$winningTeamId';
			");
			$losingTeamQuery = $database->query("
				UPDATE `" . SQLDatabase::TABLE_PREFIX . "schools` SET `totalLoses` = `totalLoses` + 1, `seasonLoses` = `seasonLoses` + 1 WHERE schoolId = '$losingTeamId';
			");
			$winningCoachQuery = $database->query("
				UPDATE `" . SQLDatabase::TABLE_PREFIX . "coaches` SET `totalWins` = `totalWins` + 1 WHERE schoolId = '$winningTeamId';
			");
			$losingCoachQuery = $database->query("
				UPDATE `" . SQLDatabase::TABLE_PREFIX . "coaches` SET `totalLoses` = `totalLoses` + 1 WHERE schoolId = '$losingTeamId';
			");
		}

		public function resetSeasonWinsAndLoses()
		{
			$database = SQLDatabase::connect();

			$result = $database->query("
				UPDATE `" . SQLDatabase::TABLE_PREFIX . "schools` SET `seasonWins` = 0, `seasonLoses` = 0;
			");

			echo("Previous season win/lose records have been reset<br>");
		}
	}
