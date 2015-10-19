
var gulp = require('gulp'),
	connect = require('gulp-connect'),
	open = require('gulp-open'),
	browserify = require('gulp-browserify'),
	concat = require('gulp-concat'),
	rename = require('gulp-rename'),
	minifyCss = require('gulp-minify-css'),
	uglify = require('gulp-uglify'),
	port = process.env.port || 8080;

/*
gulp.task('browserify', function() {
	gulp.src('./public/js/app.js')
		.pipe(browserify({ transform: 'reactify' }))
		.pipe(gulp.dest('./public/js/scripts.js'));
});
*/

gulp.task('open', function() {
	var options = {
		url: 'http://localhost:' + port,
	};
	gulp.src('./public/index.php')
		.pipe(open('', options));
});

gulp.task('connect', function() {
	connect.server({
		root: 'app',
		port: port,
		livereload: true
	});
});


gulp.task('js', function() {
	gulp.src('./public/js/**/*.js')
		.pipe(connect.reload());
});

gulp.task('html', function() {
	gulp.src('./public/*.html')
		.pipe(connect.reload());
});

gulp.task('php', function() {
	gulp.src('./public/*.php')
		.pipe(connect.reload());
});

gulp.task('watch', function() {
	gulp.watch('public/js/*.js', ['js']);
	gulp.watch('public/index.php', ['php']);
	gulp.watch('public/js/**/*.js', ['browserify']);
});


gulp.task('concat-common-js', function() {
  return gulp.src([
  		'./public/js/vendors/jquery-1.11.3.min.js',
  		'./public/js/vendors/jquery-ui-1.11.3.min.js',
  		'./public/js/vendors/bootstrap-3.3.5.min.js',
  		'./public/js/vendors/moment-2.10.6.min.js'
  	])
    .pipe(concat('vendors-common.min.js'))
    .pipe(gulp.dest('./public/js/'));
});

gulp.task('concat-highcharts', function() {
  return gulp.src([
      './public/js/vendors/highcharts.4.1.9.min.js',
      './public/js/vendors/highcharts-data.4.1.9.min.js',
      './public/js/vendors/highcharts-exporting.4.1.9.min.js',
    ])
    .pipe(concat('vendor-highcharts.js'))
    .pipe(gulp.dest('./public/js/'));
});


gulp.task('compress-js', function() {
  return gulp.src('./public/js/*.js')
  	.pipe(concat('scripts.js'))
    .pipe(uglify())
    .pipe(rename({ extname: '.min.js' }))
    .pipe(gulp.dest('./public/js/'));
});

gulp.task('minify-css', function() {
  return gulp.src([
  		'./public/css/normalize-3.0.3.min.css',
      './public/css/font-awesome.min.css', 
      './public/css/bootstrap-3.3.5.css',
      './public/css/bootstrap-select.min.css',
  		//'./public/css/bootstrap-3.3.5.min.css',
  		'./public/css/bt-override.css',
  		'./public/css/dashboard.css',
  		'./public/css/styles.css',
      './public/css/common.css'
  	])
  	.pipe(concat('styles-all.css'))
  	.pipe(minifyCss({
      keepSpecialComments: 0,
      compatibility: 'ie8'
    }))
    .pipe(rename({ extname: '.min.css' }))
    .pipe(gulp.dest('./public/css/'));
});




gulp.task('default', ['concat-js', 'minify-css']);
gulp.task('serve', ['concat-js', 'minify-css', 'connect', 'open', 'watch']);






























/*
var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */
/*
elixir(function(mix) {
    mix.sass('app.scss');
});



elixir(function(mix) {

    mix.styles([
    '../../../bower_components/bootstrap/dist/css/bootstrap.css',
    ],'public/css/bootstrap.css');
});
*/