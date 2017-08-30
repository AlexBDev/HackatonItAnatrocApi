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
                sh 'cd app/ && composer install'
                sh 'service nginx start'
            }
        }
        stage('Test') {
            steps {
                sh 'cd app/ && vendor/bin/phpunit'
                sh 'cd app/&& find src/ -name \\*.php -exec php -l "{}" \\;' // Check for php syntax errors
            }
        }
    }
}
