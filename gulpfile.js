var gulp = require('gulp');
var less = require('gulp-less');
var header = require('gulp-header');
var rename = require("gulp-rename");
var uglify = require('gulp-uglify');

// Copy vendor libraries from /node_modules into /vendor
gulp.task('copy', function () {
  gulp.src(['node_modules/swiss-styleguide/build/css/**/*'])
    .pipe(gulp.dest('Resources/Public/StyleGuide/css'));

  gulp.src(['node_modules/swiss-styleguide/build/fonts/**/*'])
    .pipe(gulp.dest('Resources/Public/StyleGuide/fonts'));

  gulp.src(['node_modules/swiss-styleguide/build/img/**/*'])
    .pipe(gulp.dest('Resources/Public/StyleGuide/img'));

  gulp.src(['node_modules/swiss-styleguide/build/js/**/*'])
    .pipe(gulp.dest('Resources/Public/StyleGuide/js'));
});
