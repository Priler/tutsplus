var gulp        = require('gulp');
var livereload = require('gulp-livereload');
var sass = require('gulp-sass');
var jade = require('gulp-jade');

gulp.task('sass', function () {

  gulp.src('./dev/css/style.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest('./public_html/media/css'))
    .pipe(livereload());

});

gulp.task('jade', function () {

  var YOUR_LOCALS = {
    pretty: true
  };
 
  gulp.src('./dev/html/*.jade')
    .pipe(jade({
      locals: YOUR_LOCALS,
      pretty: true
    }))
    .pipe(gulp.dest('./public_html/'))

});

gulp.task('default', function() {
  livereload.listen();
  gulp.watch('./dev/css/**/*.scss', ['sass']);
  gulp.watch('./dev/html/*.jade', ['jade']);
  gulp.watch('./dev/html/**/*.jade', ['jade']);
});