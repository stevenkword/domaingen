<?php
/**
 * Domain Generator
 */

namespace DOMAINGEN;

const DEBUG = false;

$datasets_dir  = __DIR__ . '/datasets';
$dataset_files = scandir( $datasets_dir, true );

/**
 * Debug output
 */
function get_nicename( $name ) {
	$nicename = $name;
	$nicename = str_replace( '.json', '', $nicename );
	$nicename = str_replace( '-', ' ', $nicename );
	$nicename = str_replace( '_', ' ', $nicename );
	return ucwords( $nicename );
}

/**
 * Debug output
 */
function debug( $data ) {
	if ( true !== DEBUG ) {
		return;
	}
	switch ( gettype( $data ) ) {
		case 'array':
		case 'object':
			print_r( $data );
			break;
		default:
			echo $data;
			break;
	}
	echo '</br><br/>';
}

/**
 * Filter out items that are not json
 */
function datasets_dir_filter( $filename ) {
	return strpos( $filename, '.json' );
}
$dataset_files = array_filter( $dataset_files, 'DOMAINGEN\datasets_dir_filter' );

$count    = 0;
$datasets = array();
foreach ( $dataset_files as $file ) {
	$dataset_file = $datasets_dir . '/' . $file;
	// debug( $file );
	$dataset_name              = strtolower( str_replace( '.json', '', $file ) );
	$datasets[ $dataset_name ] = json_decode( file_get_contents( $dataset_file ) );
}

ksort( $datasets );
// debug( $datasets );
$dataset_names = array_keys( $datasets );
debug( $datasets['tlds'] );

/* Process data */
$domains = array();
if ( isset( $_REQUEST['primary'] ) ) {
	$primaries   = array_slice( $datasets[ $_REQUEST['primary'] ], 0, 50 );
	$secondaries = array_slice( $datasets[ $_REQUEST['secondary'] ], 0, 50 );
	$tld         = $_REQUEST['tld'];

	foreach ( $primaries as $primary ) {
		foreach ( $secondaries as $secondary ) {
			$domains[] = str_replace( ' ', '', $primary . $secondary . $tld );
		}
	}

	$comma_separated = implode(",", $domains);
	$url_base = 'https://www.namecheap.com/domains/registration/results.aspx?type=beast&';
	$search_url = $url_base . http_build_query( array( 'domain' => $comma_separated ) );
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>DomainGen</title>

	<!-- Bootstrap core CSS -->
	<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!-- Custom styles for this template -->
	<link href="css/scrolling-nav.css" rel="stylesheet">
	<script>
		function copyToClipboard() {
			/* Get the text field */
			var copyText = document.getElementById("results");

			console.log( copyText );

			/* Select the text field */
			copyText.select();

			/* Copy the text inside the text field */
			var results = document.execCommand("copy");
		}
	</script>
</head>

<body id="page-top">

	<!-- Navigation -->
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
		<div class="container">
			<a class="navbar-brand js-scroll-trigger" href="index.php#page-top">DomainGen</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarResponsive">
				<ul class="navbar-nav ml-auto">
					<li class="nav-item">
						<a class="nav-link js-scroll-trigger" href="#about">About</a>
					</li>
					<li class="nav-item">
						<a class="nav-link js-scroll-trigger" href="#services">Services</a>
					</li>
					<li class="nav-item">
						<a class="nav-link js-scroll-trigger" href="#contact">Contact</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>

	<header class="bg-primary text-white">
		<form action="index.php" method="POST">
		<div class="container text-center">
			<h1>Domain search, simplified.</h1>
		</div>

		<div class="container">
			<div class="row">
				<div class="col-25">
					<label for="primary">Primary</label>
				</div>
				<div class="col-75">
					<select id="primary" name="primary">
						<?php
						foreach ( $dataset_names as $name ) {
							$selected = ( isset( $_REQUEST['primary'] ) && $name === $_REQUEST['primary'] ) ? ' selected' : '';
							echo '<option value="' . $name . '" ' . $selected . '>' . get_nicename( $name ) . '</option>';
						}
						?>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-25">
					<label for="secondary">Secondary</label>
				</div>
				<div class="col-75">
					<select id="secondary" name="secondary">
						<?php
						foreach ( $dataset_names as $name ) {
							$selected = ( isset( $_REQUEST['secondary'] ) && $name === $_REQUEST['secondary'] ) ? ' selected' : '';
							echo '<option value="' . $name . '" ' . $selected . '>' . get_nicename( $name ) . '</option>';
						}
						?>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-25">
					<label for="tld">TLD</label>
				</div>
				<div class="col-75">
					<select id="tld" name="tld">
						<?php
						foreach ( $datasets['tlds'] as $name ) {
							$selected = ( isset( $_REQUEST['tld'] ) && $name === $_REQUEST['tld'] ) ? ' selected' : '';
							echo '<option value="' . $name . '" ' . $selected . '>' . get_nicename( $name ) . '</option>';
						}
						?>
					</select>
				</div>
			</div>
			<div class="row">
				<input type="submit" value="Submit">
			</div>
		</div><!-- .container -->
		</form>
	</header>

	<section id="section-results">
		<div class="row">
			<div class="col-lg-8 mx-auto">
				<label for="results"><h2>Results</h2></label>
				<button onclick="copyToClipboard()">Copy</button>
				<button>
					<a target="_blank" href="<?php echo $search_url; ?>">Search</a>
				</button>
			</div>
			<div class="col-lg-8 mx-auto">
				<textarea id="results" name="results" rows=100><?php
				if ( isset( $_REQUEST['primary'] ) ) {
					foreach ( $primaries as $primary ) {
						foreach ( $secondaries as $secondary ) {
							echo str_replace( ' ', '', $primary . $secondary . $tld ) . "\n";
						}
					}
				}
			?></textarea>
			</div>
		</div>
	</section>

	<section id="about">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 mx-auto">
					<h2>About this page</h2>
					<p class="lead">This is a great place to talk about your webpage. This template is purposefully unstyled so you can use it as a boilerplate or starting point for you own landing page designs! This template features:</p>
					<ul>
						<li>Clickable nav links that smooth scroll to page sections</li>
						<li>Responsive behavior when clicking nav links perfect for a one page website</li>
						<li>Bootstrap's scrollspy feature which highlights which section of the page you're on in the navbar</li>
						<li>Minimal custom CSS so you are free to explore your own unique design options</li>
					</ul>
				</div>
			</div>
		</div>
	</section>

	<section id="services" class="bg-light">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 mx-auto">
					<h2>Services we offer</h2>
					<p class="lead">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aut optio velit inventore, expedita quo laboriosam possimus ea consequatur vitae, doloribus consequuntur ex. Nemo assumenda laborum vel, labore ut velit dignissimos.</p>
				</div>
			</div>
		</div>
	</section>

	<section id="contact">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 mx-auto">
					<h2>Contact us</h2>
					<p class="lead">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vero odio fugiat voluptatem dolor, provident officiis, id iusto! Obcaecati incidunt, qui nihil beatae magnam et repudiandae ipsa exercitationem, in, quo totam.</p>
				</div>
			</div>
		</div>
	</section>

	<!-- Footer -->
	<footer class="py-5 bg-dark">
		<div class="container">
			<p class="m-0 text-center text-white">Copyright &copy; Your Website 2019</p>
		</div>
		<!-- /.container -->
	</footer>

	<!-- Bootstrap core JavaScript -->
	<script src="vendor/jquery/jquery.min.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

	<!-- Plugin JavaScript -->
	<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

	<!-- Custom JavaScript for this theme -->
	<script src="js/scrolling-nav.js"></script>

</body>

</html>
