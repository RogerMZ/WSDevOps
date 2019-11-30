pipeline {
  agent any
  stages {
    stage('Init') {
      steps {
        sh 'echo "Inicio de proyecto"'
      }
    }

    stage('Dependencias') {
      steps {
        sh 'composer install'
      }
    }

  }
}