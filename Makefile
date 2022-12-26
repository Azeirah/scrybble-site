CURRENT_VERSION = $(shell cat remarks.version)

build-remarks:
	docker build -f binaries/remarks.Dockerfile binaries/remarks -t laauurraaa/remarks-bin:${CURRENT_VERSION}

push-remarks:
	docker push laauurraaa/remarks-bin:${CURRENT_VERSION}

update-remarks: build-remarks push-remarks
