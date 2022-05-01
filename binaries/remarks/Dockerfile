FROM python:3.8-buster

RUN ["mkdir", "/app"]
WORKDIR /app

COPY remarks /app

RUN ["pip", "install", "-r", "requirements.txt"]
ENTRYPOINT ["python", "-m", "remarks"]
