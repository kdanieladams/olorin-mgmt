var gulp = require('gulp');
var sass = require('gulp-sass');
var concat = require('gulp-concat');

gulp.task('sass', function() {
    gulp.src('src/assets/sass/styles.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('src/assets/css/'));
});

gulp.task('css', function(){
    gulp.src('src/assets/css/**/*.css')
        .pipe(concat('mgmt_styles.css'))
        .pipe(gulp.dest('src/public/css/'));
});

gulp.task('scripts', function() {
    gulp.src('src/assets/js/**/*.js')
        .pipe(concat('mgmt_scripts.js'))
        .pipe(gulp.dest('src/public/js/'));
});

gulp.task('default', ['sass', 'css', 'scripts'], function() {
    gulp.watch('src/assets/sass/**/*.scss', ['sass', 'css']);
    gulp.watch('src/assets/js/**/*.js', ['scripts']);
});