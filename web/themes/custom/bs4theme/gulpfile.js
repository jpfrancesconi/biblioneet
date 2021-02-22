var gulp = require('gulp');
var livereload = require('gulp-livereload')
var uglify = require('gulp-uglifyjs');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var sourcemaps = require('gulp-sourcemaps');
var imagemin = require('gulp-imagemin');
//var pngquant = require('imagemin-pngquant');




// gulp.task('imagemin', function () {
//     return gulp.src('./themes/custom/bs4theme/images/*')
//         .pipe(imagemin({
//             progressive: true,
//             svgoPlugins: [{removeViewBox: false}],
//             use: [pngquant()]
//         }))
//         .pipe(gulp.dest('./themes/custom/bs4theme/images'));
// });


gulp.task('sass', function () {
    return gulp.src('scss/bs/**/*.scss')
    .pipe(sourcemaps.init())
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(autoprefixer('last 2 version', 'safari 5', 'ie 7', 'ie 8', 'ie 9', 'opera 12.1', 'ios 6', 'android 4'))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('css'));
});


// gulp.task('uglify', function() {
//   gulp.src('lib/*.js')
//     .pipe(uglify('main.js'))
//     .pipe(gulp.dest('js'))
// });

gulp.task('watch', function(){
    livereload.listen();

    //gulp.watch('./themes/custom/bs4theme/scss/bs/**/*.scss', ['sass']);
    gulp.watch('scss/bs/**/*.scss', gulp.series('sass'));
    //gulp.watch('ib/*.js', gulp.series('uglify'));
    gulp.watch(['css/style.css', 'templates/**/*.twig', 'js/*.js'], function (files){
        livereload.changed(files)
    });
});
