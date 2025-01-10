FROM ubuntu

RUN apt update -y && apt upgrade -y
RUN apt install -y --no-install-recommends mysql-server
RUN apt clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
