<?php

	include('functions.php');

	if (!empty($_POST['merge'])) {
		// this is where we will store list of workout files to merge
		$workouts = array();

		$files = $_FILES['workout'];
		$errors = array();
		foreach ($files['error'] as $k => $v) {
			if ($v == 0) {
				if (getExtension($files['name'][$k]) == 'gpx') {
					$workouts[] = $files['tmp_name'][$k];
				} else {
					$errors[] = 'Only *.gpx files are supported';
					break;
				}
			} elseif ($v != 4) {
				$errors[] = 'Could not upload file';
				break;
			}
		}

		if (empty($errors) && count($workouts) < 2) {
			$errors[] = 'You have to upload at least 2 workouts';
		}


		if (empty($errors)) {
			$merged = mergeWorkouts($workouts);

			if ($merged) {
				header('Content-type: text/xml');
				header('Content-Disposition: attachment; filename="merged-workout.gpx"');
				header('Content-Length: '.strlen($merged));
				die($merged);
			}
		}
	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Endomondo workout merger</title>
	<link href="main.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<body>

	<div id="container">

		<h1>Endomondo workout merger</h1>

		<div class="automatic">
			<h2>Automatic merging</h2>

			<p>
				Export each workout to a gpx file and upload them with this tool in correct order. After
				submitting files, this tool will merge them and give you back "merged-workout.gpx" file
				which you should import back into Endomondo site. If everything seems ok, delete original
				workout parts from Endomondo. You should provide at least two workout files.
			</p>

			<?php if (!empty($errors)): ?>
				<div id="errors">
					<?php echo implode('<br />', $errors) ?>
				</div>
			<?php endif ?>

			<form action="index.php" method="post" enctype="multipart/form-data">
				<?php for ($n=0; $n<4; $n++): ?>
					<div class="input">
						<label>Workout:</label>
						<input name="workout[]" type="file" />
					</div>
				<?php endfor ?>

				<div class="submit">
					<input name="merge" type="submit" value="Merge" />
				</div>

			</form>

		</div>

		<div class="manual">
			<h2>Manual merging</h2>
			<ol>
				<li>Export each workout to a gpx file and give them a name like 'workout_1.gpx', 'workout_2.gpx'</li>
				<li>Open 'workout_2.gpx', find the first line that starts with <span>&lt;trkseg&gt;</span> (around line 20) and select everything from that line until the last line with <span>&lt;/trkseg&gt;</span> (that is the third line up from the bottom of the file)</li>
				<li>Copy the selection with CTRL+C</li>
				<li>Now open 'workout_1.gpx' and go all the way down</li>
				<li>Add a line before the last <span>&lt;/trkseg&gt;</span></li>
				<li>Paste the copied selection (from step 3) with CTRL+V</li>
				<li>Save the changes</li>
				<li>Import 'workout_1.gpx' again</li>
			</ol>
		</div>

	</div>

	<script type="text/javascript">
		var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
		document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">
		try {
		var pageTracker = _gat._getTracker("UA-836452-5");
		pageTracker._trackPageview();
		} catch(err) {}</script>

</body>
</html>