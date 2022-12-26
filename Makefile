build-remarks:
	docker build -f binaries/remarks.Dockerfile binaries/remarks -t laauurraaa/remarks-bin:latest

push-remarks:
	docker push laauurraaa/remarks-bin:latest

update-remarks: build-remarks push-remarks
