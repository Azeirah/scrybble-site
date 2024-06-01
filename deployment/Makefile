commit := `git rev-parse HEAD`
container := laauurraaa/scrybble-server:${commit}

release: build-app upload-app
	echo "Releasing ${container}"

build-app:
	docker build -t ${container} -f App.Dockerfile ..

upload-app:
	docker push ${container}
