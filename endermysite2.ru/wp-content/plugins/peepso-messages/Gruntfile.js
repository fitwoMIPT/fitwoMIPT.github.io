module.exports = function( grunt ) {

	grunt.initConfig({

		uglify: {
			dist: {
				options: {
					report: 'none',
					sourceMap: false
				},
				files: [{
					src: [
						'assets/js/*.js',
						'!assets/js/*-min.js',
						'!assets/js/*.min.js'
					],
					expand: true,
					ext: '.min.js',
					extDot: 'last'
				}],
			}
		}

	});

	grunt.loadNpmTasks( 'grunt-contrib-uglify' );

	grunt.registerTask( 'default', [ 'uglify' ]);

};
