<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Mover.</title>
		<meta name="author" content="Doug Black">

		<!-- CSS -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.6.2/css/bulma.min.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		<!-- script -->
		<script src="https://unpkg.com/vue@2.1.8/dist/vue.js" type="text/javascript"></script>
	</head>
	<body>
		<section class="hero is-danger">
			<div class="hero-body">
				<div class="container">
					<h1 class="title">Mover.</h1>
					<h2 class="subtitle">Move EE2 Entries to EE4</h2>
				</div>
			</div>
		</section>
		<section class="section">
			<div class="container">
				<h1 class="title">STOP...BEFORE YOU ACT</h1>
				<h2 class="subtitle">This will ruin your database. Be prepared. If you are not 100% sure you are using test databases, <a href="https://www.badgerbadgerbadger.com/">then stop and click here immediately.</a></h2>
			</div>
		</section>
		<section id="viewer">
			<div class="container">
				<div class="columns">
					<div class="column is-one-quarter">
						<h2 class="title is-3">EE2 DB (DB1)</h2>
					</div>
					<div class="column">
						<h2 class="title is-3">EE4 DB (DB2)</h2>
					</div>
				</div>
				<form action="/scripts/go.php">
					<div class="columns" v-for="f in fields1" :key="f.id">
						<div class="column is-one-quarter">
							<div class="control">
								<label v-bind:for="f.formName">{{ f.name }}<br /><small>{{ f.info }}</small></label>
							</div>
						</div>
						<div class="column">
							<div class="control">
								<div class="select">
									<p v-if="f.message">{{ f.message }}</p>
									<select v-bind:id="f.formName" v-bind:name="f.formName" v-else>
										<option value selected></option>
										<option v-for="f2 in fields2" :value="f2.id">{{ f2.name }}</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="field">
						<div class="control">
							<button class="button is-link">HIT THIS WHEN YOU'RE READY</button>
						</div>
					</div>
				</form>
			</div>
		</section>
		<!-- Bottom scripts -->
		<script src="resources/js/app.js"></script>
	</body>
</html>