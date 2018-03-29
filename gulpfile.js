var gulp = require('gulp');
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var gutil = require('gulp-util');

gulp.task('sass', function() {
    gulp.src('resources/assets/sass/styles.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('resources/assets/css/'));

    gutil.log(" ");
    gutil.log(gutil.colors.green('Sass successfully compiled!'));
    gutil.log("===========");
});

gulp.task('css', function(){
    gulp.src(['resources/assets/css/vendor/*.css',
        'resources/assets/css/*.css'])
        .pipe(concat('mgmt_styles.css'))
        .pipe(gulp.dest('public/css/'));

    gutil.log(" ");
    gutil.log(gutil.colors.green('CSS successfully concatenated and published!'));
    gutil.log("===========");
});

gulp.task('scripts', function() {
    gulp.src(['resources/assets/js/vendor/jquery-*.js',
        'resources/assets/js/vendor/dataTables.min.js',
        'resources/assets/js/vendor/bootstrap.js',
        'resources/assets/js/vendor/sweetalert.js',
        'resources/assets/js/scripts.js'])
        .pipe(concat('mgmt_scripts.js'))
        .pipe(gulp.dest('public/js/'));

    gutil.log(" ");
    gutil.log(gutil.colors.green('JavaScripts successfully concatenated and published!'));
    gutil.log("===========");
});

gulp.task('default', ['sass', 'css', 'scripts'], function() {
    gutil.log(" ");
    gutil.log(gutil.colors.yellow('Tasks completed!'));
    // gutil.log(gutil.colors.yellow('Tasks completed!  Initiating watch...'));
    gutil.log("===========");

    //gulp.watch('resources/assets/sass/**/*.scss', ['sass', 'css']);
    //gulp.watch('resources/assets/js/**/*.js', ['scripts']);
});