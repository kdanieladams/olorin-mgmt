var gulp = require('gulp');
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var gutil = require('gulp-util');

gulp.task('sass', function() {
    gulp.src('src/assets/sass/styles.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('src/assets/css/'));

    gutil.log("===========");
    gutil.log(gutil.colors.green('Sass successfully compiled!'));
    gutil.log("===========");
});

gulp.task('css', function(){
    gulp.src('src/assets/css/**/*.css')
        .pipe(concat('mgmt_styles.css'))
        .pipe(gulp.dest('src/public/css/'));

    gutil.log("===========");
    gutil.log(gutil.colors.green('CSS successfully concatenated and published!'));
    gutil.log("===========");
});

gulp.task('scripts', function() {
    gulp.src([
        'src/assets/js/vendor/jquery-*.js',
        'src/assets/js/vendor/bootstrap.js',
        'src/assets/js/vendor/sweetalert.js',
        'src/assets/js/scripts.js'
    ]).pipe(concat('mgmt_scripts.js'))
        .pipe(gulp.dest('src/public/js/'));

    gutil.log("===========");
    gutil.log(gutil.colors.green('JavaScripts successfully concatenated and published!'));
    gutil.log("===========");
});

gulp.task('default', ['sass', 'css', 'scripts'], function() {
    gutil.log("===========");
    gutil.log(gutil.colors.yellow('Tasks completed!  Initiating watch...'));
    gutil.log("===========");

    gulp.watch('src/assets/sass/**/*.scss', ['sass', 'css']);
    gulp.watch('src/assets/js/**/*.js', ['scripts']);
});