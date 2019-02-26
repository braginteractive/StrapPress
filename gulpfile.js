// Load all the modules from package.json
var gulp = require("gulp"),
  plumber = require("gulp-plumber"),
  autoprefixer = require("gulp-autoprefixer"),
  watch = require("gulp-watch"),
  jshint = require("gulp-jshint"),
  stylish = require("jshint-stylish"),
  uglify = require("gulp-uglify"),
  rename = require("gulp-rename"),
  notify = require("gulp-notify"),
  include = require("gulp-include"),
  sass = require("gulp-sass"),
  browserSync = require("browser-sync").create(),
  critical = require("critical"),
  zip = require("gulp-zip");

var config = {
  nodeDir: "./node_modules"
};

// Default error handler
var onError = function(err) {
  console.log("An error occured:", err.message);
  this.emit("end");
};

// JS to watch
var jsFiles = ["./js/**/*.js", "!./js/dist/*.js"];

// Sass files to watch
var cssFiles = ["./sass/**/*.scss"];

// automatically reloads the page when files changed
var browserSyncWatchFiles = ["./*.min.css", "./js/**/*.min.js", "./**/*.php"];

// see: https://www.browsersync.io/docs/options/
var browserSyncOptions = {
  watchTask: true,
  proxy: "http://sp:8888/"
};

// Zip files up
gulp.task("zip", function() {
  return gulp
    .src(
      [
        "*",
        "./css/*",
        "./fonts/*",
        "./images/**/*",
        "./inc/**/*",
        "./js/**/*",
        "./languages/*",
        "./sass/**/*",
        "./template-parts/*",
        "./templates/*",
        "!bower_components",
        "!node_modules"
      ],
      { base: "." }
    )
    .pipe(zip("strappress.zip"))
    .pipe(gulp.dest("."));
});

// Jshint outputs any kind of javascript problems you might have
// Only checks javascript files inside /src directory
gulp.task("jshint", function() {
  return gulp
    .src("./js/src/*.js")
    .pipe(jshint())
    .pipe(jshint.reporter(stylish))
    .pipe(jshint.reporter("fail"));
});

// Concatenates all files that it finds in the manifest
// and creates two versions: normal and minified.
// It's dependent on the jshint task to succeed.
gulp.task("scripts", function() {
  return (
    gulp
      .src("./js/manifest.js")
      .pipe(include())
      .pipe(rename({ basename: "scripts" }))
      .pipe(gulp.dest("./js/dist"))
      // Normal done, time to create the minified javascript (scripts.min.js)
      // remove the following 3 lines if you don't want it
      .pipe(uglify())
      .pipe(rename({ suffix: ".min" }))
      .pipe(gulp.dest("./js/dist"))
      .pipe(browserSync.reload({ stream: true }))
      .pipe(notify({ message: "scripts task complete" }))
  );
});

// Sass - Creates a regular and minified .css file in root
gulp.task("sass", function() {
  return gulp
    .src("./sass/style.scss")
    .pipe(plumber())
    .pipe(
      sass({
        errLogToConsole: true,
        precision: 8,
        noCache: true,
        //imagePath: 'assets/img',
        includePaths: [config.nodeDir + "/bootstrap/scss"]
      }).on("error", sass.logError)
    )
    .pipe(autoprefixer())
    .pipe(gulp.dest("."))
    .pipe(sass({ outputStyle: "compressed" }).on("error", sass.logError))
    .pipe(rename({ suffix: ".min" }))
    .pipe(gulp.dest("."))
    .pipe(browserSync.reload({ stream: true }))
    .pipe(notify({ title: "Sass", message: "sass task complete" }));
});

// Generate & Inline Critical-path CSS
gulp.task("critical", function(cb) {
  critical.generate({
    base: "./",
    src: "http://sp:8888/",
    dest: "css/home.min.css",
    ignore: ["@font-face"],
    dimensions: [
      {
        width: 320,
        height: 480
      },
      {
        width: 768,
        height: 1024
      },
      {
        width: 1280,
        height: 960
      }
    ],
    minify: true
  });
});

// Starts browser-sync task for starting the server.
gulp.task("browser-sync", function() {
  browserSync.init(browserSyncWatchFiles, browserSyncOptions);
});

// Start the livereload server and watch files for change
gulp.task("watch", function() {
  // don't listen to whole js folder, it'll create an infinite loop
  // run jshint be compiling js code
  gulp.watch(jsFiles, gulp.series("jshint", gulp.parallel("scripts")));

  gulp.watch(cssFiles, gulp.parallel("sass"));
});

gulp.task("default", gulp.parallel("watch", "browser-sync"));
