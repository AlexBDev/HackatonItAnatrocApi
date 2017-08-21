pipeline {
    agent {
        docker {
            image 'abdev/anatroc-php'
        }
    }
    stages {
        stage('Build') {
            steps {
                sh 'php --version'
                sh 'cd app/api.anatroc && composer install'
                sh 'service nginx start'
            }
        }
        stage('Test') {
            steps {
                sh 'cd app/api.anatroc && vendor/bin/phpunit'
                sh 'cd app/api.anatroc && find src/ -name \\*.php -exec php -l "{}" \\;' // Check for php syntax errors
            }
        }
    }
}
