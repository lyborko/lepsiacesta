const { src, dest, series, watch} = require('gulp');
const uglify = require('gulp-uglify');
const rename = require('gulp-rename');
const sourcemaps = require('gulp-sourcemaps');
const autoprefixer = require('gulp-autoprefixer');
//import * as dartSass from 'sass';
const dartSass= require('sass');
const gulpSass = require('gulp-sass');
const sass = gulpSass(dartSass);
//const livereload = require('gulp-livereload');

function uglifyTask() {
  return src('themes/custom/betterway/src/js/*.js')
    // The gulp-uglify plugin won't update the filename
    .pipe(uglify())
    // So use gulp-rename to change the extension
    .pipe(rename({ extname: '.min.js' }))
    .pipe(dest('themes/custom/betterway/js/'));
}

function sassTask() {
    return src('themes/custom/betterway/src/sass/**/*.scss')
      .pipe(sourcemaps.init())
          .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
          .pipe(autoprefixer('last 2 version', 'safari 5', 'ie 7', 'ie 8', 'ie 9', 'opera 12.1', 'ios 6', 'android 4'))
      .pipe(sourcemaps.write('./'))
      .pipe(dest('themes/custom/betterway/css'));
  };

  function watchTask (){
    //livereload.listen();
    watch('themes/custom/betterway/src/sass/**/*.scss', sassTask);
    watch('themes/custom/betterway/src/js/*.js', uglifyTask);
    //watch(['themes/custom/betterway/css/*.css', 'themes/custom/betterway/php/*.php', 'themes/custom/betterway/js/*.js'], function (files){
    //    livereload.changed(files)
    //});
  };

exports.default = series(watchTask); //uglifyTask, sassTask, 