#
# STAGE 2: php
#
FROM registry.tools.orange-sonatel.com/php/php74-ubuntu-apache

#ARG USER=docker
#ARG UID=1000
#ARG GID=1000
# default password for user
#ARG PW=docker


#RUN groupadd -g 1000 -o $USER
#RUN useradd -m -u 1000 -g 1000 -G www-data -o -s /bin/bash $USER
#RUN echo "${USER}:${PW}" | chpasswd

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html
RUN chmod 777 /tmp/
RUN chmod o+rwx /var/lib/php/sessions/
RUN chown -R www-data:www-data /var/lib/php/sessions/

EXPOSE 80 80/tcp
