/**
 * Popular Tasks
 * -------------
 *
 * compile: compiles the .less files of the specified packages
 * lint: runs jshint on all .js files
 */

var merge = require('merge-stream'),
    gulp = require('gulp'),
    header = require('gulp-header'),
    less = require('gulp-less'),
    rename = require('gulp-rename'),
    eslint = require('gulp-eslint'),
    plumber = require('gulp-plumber'),
    fs = require('fs'),
    path = require('path');

// paths of the packages for the compile-task
var pkgs = [
    {path: 'app/installer/', data: '../../composer.json'},
    {path: 'app/system/modules/theme/', data: '../../../../composer.json'}
];

var assetConfig = {
    npm_source: 'node_modules',
    mappings: [
        {
            target: 'app',
            configs: [
                {module: 'codemirror', files: {src: ['**']}},
                {module: 'jquery', files: {src: ['dist/**', 'LICENSE.txt', 'package.json']}},
                {module: 'lodash', files: {src: ['lodash*.js', 'LICENSE', 'package.json']}},
                {module: 'marked', files: {src: ['marked*.js', 'LICENSE.md', 'package.json']}},
                {module: 'uikit', files: {src: ['**']}},
                {module: 'vue', files: {src: ['dist/**', 'LICENSE', 'package.json']}},
                {module: 'easymde', files: {src: ['dist/**', 'LICENSE', 'package.json']}},
                {module: 'vue-multiselect', files: {src: ['dist/**', 'LICENSE', 'package.json']}},
            ]
        }
    ]
};

// banner for the css files
var banner = "/*! <%= data.title %> <%= data.version %> | (c) 2014 Biskuit | MIT License */\n";

var cldr = {
    cldr: path.join(__dirname, 'node_modules/cldr-core/supplemental/'),
    intl: path.join(__dirname, 'app/system/modules/intl/data/'),
    locales: path.join(__dirname, 'node_modules/cldr-localenames-modern/main/'),
    formats: path.join(__dirname, 'app/assets/vue-intl/dist/locales/'),
    languages: path.join(__dirname, 'app/system/languages/')
};

// general error handler for plumber
var errhandler = function (error) {
    this.emit('end');
    return console.error(error.toString());
};

function assets () {
    return merge.apply(null, assetConfig.mappings.map(function (mapping) {
        let assetsFolder = mapping.target + '/assets';
        return mapping.configs.map(function (config) {
            let sourceFolder = assetConfig.npm_source + '/' + config.module;
            let src = [];
            for (let path of config.files.src)
                src.push(sourceFolder + '/' + path);
            let dest = assetsFolder + '/' + config.module + (config.files.hasOwnProperty('dest') ? '/' + config.files.dest : '');
            return gulp.src(src)
                .pipe(gulp.dest(dest));
        });
    }));
};

/**
 * Compile all less files
 */
function compile () {

    pkgs = pkgs.filter(function (pkg) {
        return fs.existsSync(pkg.path);
    });

    return merge.apply(null, pkgs.map(function (pkg) {
        return gulp.src(pkg.path + '**/less/*.less', {base: pkg.path})
            .pipe(plumber(errhandler))
            .pipe(less({compress: true, relativeUrls: true}))
            .pipe(header(banner, {data: require('./' + pkg.path + pkg.data)}))
            .pipe(rename(function (file) {
                // the compiled less file should be stored in the css/ folder instead of the less/ folder
                file.dirname = file.dirname.replace('less', 'css');
            }))
            .pipe(gulp.dest(pkg.path));
    }));
}

/**
 * Watch for changes in files
 */
function watch (cb) {
    gulp.watch('**/*.less', gulp.parallel('compile'));
    cb();
}

/**
 * Lint all script files
 */
function lint () {
    return gulp.src([
        'app/modules/**/*.js',
        'app/system/**/*.js',
        'extensions/**/*.js',
        'themes/**/*.js',
        '!**/bundle/*',
        '!**/vendor/**/*',
        '!**/assets/**/*',
        '!node_modules/**',
        '!.git/**'
    ])
        .pipe(eslint())
        .pipe(eslint.format())
        .pipe(eslint.failOnError());
}

function cldr (cb) {

    // territoryContainment
    var data = {}, json = JSON.parse(fs.readFileSync(cldr.cldr + 'territoryContainment.json', 'utf8')).supplemental.territoryContainment;
    Object.keys(json).forEach(function (key) {
        if (isNaN(key)) return;
        data[key] = json[key]._contains;
    });
    fs.writeFileSync(cldr.intl + 'territoryContainment.json', JSON.stringify(data));

    fs.readdirSync(cldr.languages)
        .filter(function (file) {
            return fs.statSync(path.join(cldr.languages, file)).isDirectory();
        })
        .forEach(function (src) {

            var id = src.replace('_', '-'), shortId = id.substr(0, id.indexOf('-')), found;

            ['languages', 'territories'].forEach(function (name) {

                found = false;
                [id, shortId, 'en'].forEach(function (locale) {
                    var file = cldr.locales + locale + '/' + name + '.json';
                    if (!found && fs.existsSync(file)) {
                        found = true;
                        fs.writeFileSync(cldr.languages + src + '/' + name + '.json', JSON.stringify(JSON.parse(fs.readFileSync(file, 'utf8')).main[locale].localeDisplayNames[name]));
                    }
                });

            });

            found = false;
            [id.toLowerCase(), shortId, 'en'].forEach(function (locale) {
                var file = cldr.formats + locale + '.json';
                if (!found && fs.existsSync(file)) {
                    found = true;
                    fs.writeFileSync(cldr.languages + src + '/formats.json', fs.readFileSync(file, 'utf8'));
                }
            });

        });
    cb();
}

exports.default = gulp.series(assets, compile);
exports.assets = assets;
exports.compile = compile;
exports.lint = lint;
exports.cldr = cldr;
exports.watch = watch;
