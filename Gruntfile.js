module.exports = function(grunt){

	grunt.initConfig({
		concat: {
			options: {
				separator: ';',
			},
			dist: {
				src: [
					'web/js/jquery/jquery.js',
                    'web/js/superfish/superfish.js',
                    'web/js/owl/owl.carousel.min.js',
                    'web/js/lightbox/lightbox.js',
					'web/js/client/app.js',
				],
				dest: 'web/js/client/app_min.js',
			},
		},

		uglify: {
			dist: {
				files: {
					'web/js/client/app_min.js': ['web/js/client/app_min.js']
				}
			}
		}
	});	

	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');

	grunt.registerTask('default',['concat','uglify']);

}