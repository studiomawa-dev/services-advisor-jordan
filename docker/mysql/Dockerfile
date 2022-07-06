FROM mysql:8.0

CMD [ "--default-authentication-plugin=mysql_native_password" ]
COPY ./dump/ /docker-entrypoint-initdb.d/
COPY ./conf/ /etc/mysql/conf.d/

COPY ./prepare-image.sh /
RUN chmod +x /prepare-image.sh
RUN /prepare-image.sh && rm -f /prepare-image.sh

ENV MYSQL_UNIX_PORT /var/lib/mysql/mysql.sock

COPY ./docker-entrypoint.sh /entrypoint.sh
COPY ./healthcheck.sh /healthcheck.sh

RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
HEALTHCHECK CMD /healthcheck.sh