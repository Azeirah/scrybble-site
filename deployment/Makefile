commit := `git rev-parse HEAD`
container := laauurraaa/scrybble-server:${commit}

release: build-app upload-app
	echo "Releasing ${container}"

build-app:
	docker build -t ${container} --build-arg RELEASE_HASH=${commit} -f App.Dockerfile ..

upload-app:
	docker push ${container}
