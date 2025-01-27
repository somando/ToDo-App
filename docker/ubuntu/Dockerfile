FROM ubuntu

WORKDIR /home/ubuntu

RUN apt update -y && apt upgrade -y
RUN apt install -y --no-install-recommends mysql-server
RUN apt clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY structure.sql create_tables.sh ./
RUN chmod +x create_tables.sh
