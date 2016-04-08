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

gulp.task('minify-common-js', function() {
  return gulp.src('./public/js/common.js')
    .pipe(uglify())
    .pipe(rename({ extname: '.min.js' }))
    .pipe(gulp.dest('./public/js/'));
});

gulp.task('concat-common-js', function() {
  return gulp.src([
  		'./public/js/vendors/jquery-1.11.3.min.js',
  		'./public/js/vendors/jquery-ui-1.11.3.min.js',
  		'./public/js/vendors/bootstrap-3.3.5.min.js',
      './public/js/vendors/moment-2.10.6.min.js',
      './public/js/vendors/bootstrap-datetimepicker-4.17.37.min.js',
      './public/js/vendors/underscore-1.8.3.min.js',
      './public/js/vendors/accounting-0.4.1.min.js',
  		'./public/js/vendors/jquery.tablesorter-2.25.7.min.js',
      './public/js/common.min.js'
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
  		'./public/css/bootstrap-datetimepicker-4.17.37.min.css',
  		'./public/css/bt-override.css',
  		'./public/css/dashboard.css',
  		'./public/css/styles.css',
      './public/css/common.css',
      './public/css/dropbox.css'
  	])
  	.pipe(concat('styles-all.css'))
  	.pipe(minifyCss({
      keepSpecialComments: 0,
      compatibility: 'ie8'
    }))
    .pipe(rename({ extname: '.min.css' }))
    .pipe(gulp.dest('./public/css/'));
});


gulp.task('copy-css', function() {
  return gulp.src([
      './public/css/styles-all.min.css'
    ])
    .pipe(gulp.dest('../gi-boss/public/css/'));
});

gulp.task('copy-js', function() {
  return gulp.src([
      './public/js/vendors-common.min.js'
    ])
    .pipe(gulp.dest('../gi-boss/public/js/'));
});

gulp.task('copy-fonts', function() {
  return gulp.src([
      './public/fonts/*.*'
    ])
    .pipe(gulp.dest('../gi-boss/public/fonts/'));
});

gulp.task('copy-images', function() {
  return gulp.src([
      './public/images/favicon.ico',
      './public/images/g.png',
      './public/images/giligans-header.gif',
      './public/images/giligans-header.png',
      './public/images/giligans-logo.png',
      './public/images/gililogo.png',
      './public/images/login-avatar.png'
    ])
    .pipe(gulp.dest('../gi-boss/public/images/'));
});

gulp.task('copy-assets', ['copy-css', 'copy-js', 'copy-fonts', 'copy-images']);


gulp.task('default', ['concat-js', 'minify-css']);
gulp.task('serve', ['concat-js', 'minify-css', 'connect', 'open', 'watch']);