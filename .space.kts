job("Deploy") {
    container(displayName="Build", image="laauurraaa/php-composer-node-npm") {
        shellScript {
            content = """
                composer install --no-interaction --no-dev
                npm ci
                npm run prod
                zip -r main_release.zip . -x .gitignore -x .space.kts -x .git/\*
                cp main_release.zip /mnt/space/share
            """
        }
    }

    container(displayName = "Deploy", image = "amazon/aws-cli") {
        env["APP_NAME"] = Params("app_name")
        env["S3_BUCKET"] = Params("s3_bucket")
        env["AWS_ENV_NAME"] = Params("aws_environment_name")
        env["AWS_ACCESS_KEY_ID"] = Secrets("aws_access_key_id")
        env["AWS_SECRET_ACCESS_KEY"] = Secrets("aws_secret_access_key")

        shellScript {
            content = """
                aws configure set region eu-central-1
                aws s3 cp /mnt/space/share/main_release.zip s3://${'$'}S3_BUCKET/main_release.zip
                aws elasticbeanstalk create-application-version --application-name ${'$'}APP_NAME --version-label ${'$'}JB_SPACE_EXECUTION_NUMBER --source-bundle S3Bucket=${'$'}S3_BUCKET,S3Key=main_release.zip
                aws elasticbeanstalk update-environment --application-name ${'$'}APP_NAME --environment-name ${'$'}AWS_ENV_NAME --version-label ${'$'}JB_SPACE_EXECUTION_NUMBER
            """
        }
    }

    startOn {
        gitPush { enabled = true }
    }
}
