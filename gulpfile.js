var gulp = require('gulp');
var gutil = require('gulp-util');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
//var minifyCSS = require('gulp-minify-css');
//var browserSync = require('browser-sync');

// Paths
var basePath = './';
var cssPath = basePath + 'web/css/front/';

gulp.task('css', function () {

	gulp.src(cssPath + 'scss/**/*')
		.pipe(sourcemaps.init())
		.pipe(sass({debugInfo: true, errLogToConsole: true}))
		.pipe(sourcemaps.write('.'))
		.pipe(gulp.dest(cssPath + 'compiled'));

    gulp.src(cssPath + 'scss/main.scss')
        .pipe(sourcemaps.init())
        .pipe(sass({debugInfo: true, errLogToConsole: true}))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(cssPath));
});

gulp.task('watch', ['css'], function () {
    gulp.watch(cssPath + 'scss/**/*.scss', ['css']);
});

gulp.task('default', ['css']);