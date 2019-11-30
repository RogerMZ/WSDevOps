pipeline {
  agent any
  stages {
    stage('Entorno') {
      parallel {
        stage('Inicio Entorno') {
          steps {
            sh 'echo "Inicio de proyecto"'
          }
        }

        stage('elimina locks') {
          steps {
            sh '''alias composer=\'php /opt/composer/composer.phar\'

echo "Aqui elimino composer.lock"
#rm composer.lock'''
          }
        }

      }
    }

    stage('Dependencias') {
      steps {
        sh '''alias composer=\'php /opt/composer/composer.phar\'

composer install'''
      }
    }

  }
}