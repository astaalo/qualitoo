pipeline {
    /* choisir un slave Jenkins qui a le label php7 */
    agent  {
        label 'dst-preprod'
    }
    environment {
        EMAIL_RECIPIENTS = 'MoctarThiam.MBODJ@orange-sonatel.com, Madiagne.Sylla@orange-sonatel.com, Mohamed.SALL@orange-sonatel.com, abdoulaye.fall3@orange-sonatel.com'
        IMAGE = 'registry.tools.orange-sonatel.com/dd/coris-web'
        VERSION = readMavenPom().getVersion()
        NAME = readMavenPom().getArtifactId()
        SERVICE_NAME = "${ARTIFACT_ID}-db"
        PROJECT_ENV = 'sonatelsa-coris-rec'
        APP_ENV = 'prod'
        APP_DEBUG = '0'
        APP_SECRET = '777fa4b759d4067ed9ee76f6b0d9d156'
        MAILER_URL="smtp://10.100.56.56:25"
        DATABASE_URL="mysql://coris:crs_pma@s2m@172.17.0.1:3306/coris?serverVersion=13&charset=utf8"
    }
    tools {
        maven "Maven_3.3.9"
    }
    stages {
        stage('Installation des packets') {
            steps {
                sh 'rm -rf vendor'
                sh 'php74 -d memory_limit=-1 composer.phar update'
            }
        }
        /*stage('SonarQube Scan') {
            steps{
                script{
                    withSonarQubeEnv('SonarQubeServer') {
                        sh 'mvn sonar:sonar -X'
                    }
                }
            }
        }*/
        /*stage("SonarQube Quality Gate") {
            steps{
                script{
                    timeout(time: 25, unit: 'MINUTES') {
                    def qg = waitForQualityGate()
                    if (qg.status != 'OK') {
                        error "Pipeline aborted due to quality gate failure: ${qg.status}"
                    }
                }
        
                }
            }
        }*/
        stage('Build & Push Docker image') {
            when {
                anyOf { branch 'master' }
            }
            options { skipDefaultCheckout() }
            steps {
                sh 'docker ps -qa -f name=${NAME} | xargs --no-run-if-empty docker rm -f'
                sh 'docker images -f reference=${IMAGE} -qa | xargs --no-run-if-empty docker rmi'
                sh 'rm -rf target/'
                sh 'sed -i "/DATABASE_URL/ s/^/# /" .env'
                sh 'sed -i "/MAILER_URL/ s/^/# /" .env'
                sh 'sed -i "/APP_SECRET/ s/^/# /" .env'
                sh 'sed -i "/APP_DEBUG/ s/^/# /" .env'
                sh 'sed -i "/APP_ENV/ s/^/# /" .env'
                sh 'docker build --no-cache -t ${IMAGE}:${VERSION} .'
                sh 'docker push ${IMAGE}:${VERSION}'
            }
        }

        stage(' Deploy IN Dev') {
            steps {
                sh 'docker run --name=${NAME} -d --restart=always -e DATABASE_URL=$DATABASE_URL -e MAILER_URL=$MAILER_URL -e APP_ENV=$APP_ENV -e APP_DEBUG=$APP_DEBUG -e APP_SECRET=$APP_SECRET --memory-reservation=256M --memory=512M -p 8066:80 -p 2266:22 ${IMAGE}:${VERSION}'
            }
        }
/*
		stage('Malaw - Mysql service') {
            agent {label "malaw4-prod"}
                when {
                    allOf {
                        expression {
                            openshift.withCluster() {
                                openshift.withProject("${PROJECT_ENV}") {
                                return !openshift.selector("svc", "${SERVICE_NAME}").exists();
                            }
                        }
                    }
                }
            }
            steps {
                //Generate maven-resource-plugin param files"
                sh 'mvn validate'
                script {
                    openshift.withCluster() {
                        openshift.withProject("${PROJECT_ENV}") {
                            //Process external-service-mysqld template for external mysql service
                            def models =  openshift.process( "openshift//sonatel-mysql-persistent","--param-file=openshift/mysql-develop.params")
 
                            //Adding labels
                            for ( o in models ) {
                                o.metadata.labels[ "env" ] = "${ENV_APP}"
                                o.metadata.labels[ "type" ] = "mysql-db"
                                o.metadata.labels[ "app" ] = "${NAME}"
                                o.metadata.labels[ "from" ] = "jenkins-pipeline"
                            }
 
                            //Create objects processed
                            def created = openshift.apply( models )
                        }
                    }
                }
            }
        }
*/
/*
        stage('Malaw - Deploy') {
            agent {label "malaw4-prod"}
            steps {
                //Generate maven-resource-plugin param files"
                sh 'mvn validate'
                script {
                    openshift.withCluster() {
                        openshift.withProject("${PROJECT_ENV}") {
                            //Process spring-boot-image-docker-mysqldb template for app deployment
                            def models =  openshift.process( "openshift//sonatel-symfony-image-docker-mysqldb","--param-file=openshift/app-develop.params","-p IMAGE_DOCKER_TAG=${VERSION}")

                            //Adding labels
                            for ( o in models ) {
                                o.metadata.labels[ "env" ] = "${ENV_APP}"
                                o.metadata.labels[ "type" ] = "php-app"
                                o.metadata.labels[ "project" ] = "e-annuaire"
                                o.metadata.labels[ "perimetre" ] = "e-annuaire"
                                o.metadata.labels[ "tier" ] = "frontend"
                                o.metadata.labels[ "criticity" ] = "C1"
                                o.metadata.labels[ "app" ] = "${NAME}"
                                o.metadata.labels[ "from" ] = "jenkins-pipeline"
                                o.metadata.labels[ "version" ] = "${VERSION}"
                            }
    
                            //Create objects processed
                            def created = openshift.apply( models )
                            //Adding more environment variables
//                            openshift.raw("set env dc/${NAME} APP_ENV=${ENV_APP}")
//                            openshift.raw("set env dc/${NAME} DATABASE_URL=")
    
                            def dc = openshift.selector('dc', "${NAME}")
                            dc.rollout().status()
                        }
                    }
                }
            }
        }
*/

        /*stage('Launch Qualys Scan') {
            agent  {
                label 'qualys'
            }
            steps {
                sh 'mvn clean qualys:scan -X'
            }
        }
        stage('Check Qualys Scan') {
            agent  {
                label 'qualys'
            }
            steps {
                script {
                    timeout(time:240, unit: 'MINUTES') {
                        waitUntil {
                            sleep time: 7, unit: 'MINUTES'
                            try {
                                sh 'mvn qualys:check'
                                def result = manager.logContains(".*SCAN-FINISHED*.")
                                if(result)
                                    return true
                            } catch(exc) {
                                echo 'Une exception a été rencontrée... Retry en cours'
                            }
                            return false
                        }
                    }
                }
            }
        }
        stage('Analyse Qualys Report') {
            agent  {
                label 'qualys'
            }
            steps {
                script{
                    try{
                        sh 'mvn qualys:analyse-prepare'
                    } catch(exc) {
                        echo 'Une exception a été rencontrée pendant qualys:analyse-prepare'
                    }
                    sleep time: 1, unit: 'MINUTES'
                    try {
                        sh 'mvn qualys:analyse-perform'
                    } catch(exc) {
                        echo 'Une exception a été rencontrée pendant qualys:analyse-perform'
                    }
                }
            }
        }
        stage('Qualys Download Report') {
            agent  {
                label 'qualys'
            }
            steps {
                sh 'mvn qualys:report'
                sleep time: 1, unit: 'MINUTES'
                sh 'mvn qualys:download'
            }
            post{
                success {
                    archiveArtifacts artifacts: 'target/qualys/*.pdf'
                    emailext attachmentsPattern: 'target/qualys/*.pdf',
                    body: 'Rapport Qualys joint au mail.',
                    subject: '[QUALYS] Rapport Vulnerabilité',
                    to: 'Madiagne.sylla@orange-sonatel.com, mohamed.sall@orange-sonatel.com, moctarthiam.mbodj@orange-sonatel.com'
                }
            }
        }*/
        
    }

    post {
        changed {
            emailext attachLog: true, body: '$DEFAULT_CONTENT', subject: '$DEFAULT_SUBJECT', to: '$EMAIL_RECIPIENTS'
        }
        failure {
            emailext attachLog: true, body: '$DEFAULT_CONTENT', subject: '$DEFAULT_SUBJECT', to: '$EMAIL_RECIPIENTS'
        }
    }
}

