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
            sh '''echo "Aqui elimino composer.lock"
#rm composer.lock'''
          }
        }

      }
    }

    stage('Dependencias') {
      steps {
        sh 'composer'
      }
    }

  }
}