FROM mysql:latest
RUN ln -sf /usr/share/zoneinfo/Europe/Bratislava /etc/localtime && echo "Europe/Bratislava" > /etc/timezone

ENV MYSQL_ROOT_PASSWORD="root"
ENV MYSQL_PASSWORD=""
ENV MYSQL_DATABASE="pkrim-art-gallery"

COPY init.sql /docker-entrypoint-initdb.d/

CMD ["mysqld"]